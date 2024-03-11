<?php
$this->theme->add($this->url . 'assets/css/select2.min.css', '', 'select2-css');
$this->theme->add($this->url . 'assets/js/select2.full.min.js', '', 'bootstrap-select2');
?>
<?php echo $this->renderWidget('core::notification', []); ?>
<div class="container-fluid report-usercase-preview align-items-center row justify-content-center mx-auto pt-3">
    <div class="row">
        <div class="col-lg-8 col-sm-12 diagrams-column position-relative">
            <input id="input_title" type="hidden" class="d-none" name="title">
            <?php if($this->data && isset($this->data['note_diagrams']) && $this->data['note_diagrams']) :?>
                <div class="position-absolute select-diagrams">
                    <select id="select-diagram" class="form-select">
                        <?php foreach($this->data['note_diagrams'] as $index => $note): ?>
                            <option <?php echo $index ? '' : 'selected'; ?> value="<?php echo $note['id'] ?>"><?php echo $note['title'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="position-absolute toggle-description">
                <button class="btn border button-toggle-description bg-white">[ - ]</button>
            </div>
            <div id="diagrams" class="mermaid-container border-0">
                <?php if($this->data && isset($this->data['note_diagrams']) && $this->data['note_diagrams']) :?>
                    <?php foreach($this->data['note_diagrams'] as $note): ?>
                        <?php if($note['id']) : ?>
                            <div id="item-diagram-<?php echo $note['id']?>" class="item-diagram">
                            <?php 
                                $this->_view->setVar('currentId', $note['id']);
                                echo $this->renderWidget('note_'. $note['type'].'::preview'); ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-4 col-sm-12 description-column">
            <div id="description-diagrams" class="description-container">
                <?php if($this->data && isset($this->data['note_description']) && $this->data['note_description']) :?>
                    <?php foreach($this->data['note_description'] as $note): ?>
                        <?php if($note['id']) : ?>
                            <?php 
                                $this->_view->setVar('currentId', $note['id']);
                                echo $this->renderWidget('note_'. $note['type'].'::preview'); ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php echo $this->render('backend.preview.javascript');