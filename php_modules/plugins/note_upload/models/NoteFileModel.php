<?php
/**
 * SPT software - Model
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic model
 * 
 */

namespace App\plugins\note_upload\models;

use SPT\Container\Client as Base;

class NoteFileModel extends Base
{ 
    use \SPT\Traits\ErrorString;
    
    public function getDetail($id)
    {
        if (!$id)
        {
            return false;
        }

        $note = $this->NoteEntity->findByPK($id);
        if (!$note)
        {
            return [];
        }

        $file = $this->FileEntity->findOne(['note_id' => $id]);
        $note['path'] = $file ? $file['path'] : '';
        $note['file_type'] = $file ? $file['file_type'] : '';

        return $note;
    }

    public function getCurrentId()
    {
        $params = $this->request->get('urlVars');
        $id = $params['id'] ?? 0;
        return (int) $id;
    }

    public function add($data)
    {
        $data['tags'] = isset($data['tags']) ? $this->TagModel->convert($data['tags']) : '';
        $convert = isset($data['share_user']) ? $this->ShareUserModel->convert($data['share_user']) : [];
        $data['share_user'] = isset($convert['users']) ? $convert['users'] : '';
        $data['share_user_group'] = isset($convert['groups']) ? $convert['groups'] : '';
        $files = [];

        if (is_array($data['file']['name']))
        {
            for ($i=0; $i < count($data['file']['name']); $i++) 
            { 
                $tmp = $data;
                $tmp['title'] = $data['title'] ? $data['title'] : $data['file']['name'][$i];
                $tmp['file'] = [
                    'name' => $data['file']['name'][$i],
                    'full_path' => $data['file']['full_path'][$i],
                    'type' => $data['file']['type'][$i],
                    'tmp_name' => $data['file']['tmp_name'][$i],
                    'error' => $data['file']['error'][$i],
                    'size' => $data['file']['size'][$i],
                ];

                $files[] = $tmp;
            }
        }
        else
        {
            $files[] = $data;
        }

        foreach($files as $item)
        {
            $file_name = $this->upload($item['file']);
            if (!$file_name)
            {
                return false;
            }

            $note = [
                'title' => $item['title'],
                'public_id' => '',
                'alias' => '',
                'data' => '',
                'tags' => $item['tags'],
                'share_user' => $item['share_user'],
                'share_user_group' => $item['share_user_group'],
                'type' => 'upload',
                'status' => isset($item['status']) ? $item['status'] : 0,
                'note_ids' => isset($item['note_ids']) ? $item['note_ids'] : '',
                'notice' => isset($item['notice']) ? $item['notice'] : '',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $this->user->get('id'),
                'locked_at' => date('Y-m-d H:i:s'),
                'locked_by' => $this->user->get('id'),
            ];

            $note = $this->NoteEntity->bind($note);
            if (!$note)
            {
                $this->error = $this->NoteEntity->getError();
                return false;
            }

            $newId =  $this->NoteEntity->add($note);

            if ($newId)
            {
                $file_type = explode('.', $file_name);
                $file_type = strtolower(end($file_type));
                $file = $this->FileEntity->bind([
                    'note_id' => $newId,
                    'path' => 'media/attachments/' . date('Y/m/d'). '/'. $file_name,
                    'file_type' => $file_type,
                ]);

                if (!$file)
                {
                    $this->error = $this->FileEntity->getError();
                    return false;
                }

                $try = $this->FileEntity->add($file);

                if (!$try)
                {
                    $this->error = 'Error: Can\'t create the record.';
                    return false;
                }
            }
            else
            {
                $this->error = $this->NoteEntity->getError();
                return false;
            }

        }

        return $newId;
    }

    public function update($data)
    {
        $data['tags'] = isset($data['tags']) ? $this->TagModel->convert($data['tags']) : '';
        $convert = isset($data['share_user']) ? $this->ShareUserModel->convert($data['share_user']) : [];
        $data['share_user'] = isset($convert['users']) ? $convert['users'] : '';
        $data['share_user_group'] = isset($convert['groups']) ? $convert['groups'] : '';
        $data = $this->NoteEntity->bind($data);

        if (!$data)
        {
            $this->error = $this->NoteEntity->getError();
            return false;
        }

        $try = $this->NoteEntity->update([
            'title' => $data['title'],
            'tags' => $data['tags'],
            'share_user' => $data['share_user'],
            'share_user_group' => $data['share_user_group'],
            'notice' => isset($data['notice']) ? $data['notice'] : '',
            'id' => isset($data['id']) ? $data['id'] : 0,
        ]);

        if (!$try)
        {
            $this->error = 'Error: Can\'t update the record.';
        }

        return $try;
    }

    public function upload($file)
    {
        if($file && $file['name']) 
        {
            // get folder save attachment
            $path_attachment = $this->createFolderSave();

            $findMime = null;
            if ($this->config->exists('extensionAllow') && $this->config->extensionAllow &&  is_array($this->config->extensionAllow)) 
            {
                $findMime = $this->config->extensionAllow;
            }

            $uploader = $this->file->setOptions([
                'overwrite' => true,
                'targetDir' => $path_attachment,
                'findMime' => $findMime,
            ]);
    
            // TODO: create dynamice fieldName for file
            $index = 0;
            $tmp_name = $file['name'];
            while(file_exists($path_attachment. '/' . $file['name']))
            {
                $file['name'] = $index. "_". $tmp_name;
                $index ++;
            }
            
            if( false === $uploader->upload($file) )
            {
                $this->error = $uploader->getError();
                return false;
            }
            
            return $file['name'];
        }

        return false;
    }

    public function remove($id)
    {
        if (!$id)
        {
            $this->error = 'Invalid id';
            return false;
        }

        $file = $this->FileEntity->findOne(['note_id' => $id]);
        if (!$file)
        {
            $this->error = 'Invalid File';
            return false;
        }

        // remove file
        if (file_exists(PUBLIC_PATH. $file['path']))
        {
            if (!unlink(PUBLIC_PATH. $file['path']))
            {
                $this->error = 'Can`t remove file';
                return false;
            }
        }

        // remove note
        $this->FileEntity->remove($file['id']);
        $try = $this->NoteEntity->remove($id);

        return $try;
    }

    public function createFolderSave($dir = '')
    {
        if (!$dir) {
            $dir = MEDIA_PATH ;
        }

        foreach (['attachments', date('Y'), date('m'), date('d')] as $item) 
        {
            $dir .= '/' . $item;

            if (!is_dir($dir) && !mkdir($dir)) 
            {
                return '';
            }
        }
        return $dir;
    }

    public function isImage($path)
    {
        if(@is_array(getimagesize($path))){
            return true;
        } else {
            return false;
        }
    }
}
