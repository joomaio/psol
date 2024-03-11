<?php
$this->theme->add($this->url . 'assets/css/select2.min.css', '', 'select2-css');
$this->theme->add($this->url . 'assets/js/select2.full.min.js', '', 'bootstrap-select2');
?>
<?php echo $this->renderWidget('core::notification', []); ?>
<div class="container-fluid align-items-center row justify-content-center mx-auto pt-3">
    <form enctype="multipart/form-data" action="<?php echo $this->link_form . '/' . $this->id ?>" method="post" id="form_submit" class="report-usercase">
        <input id="input_title" type="hidden" class="d-none" name="title">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-sm-6 position-relative">
                <div class="row">
                    <div class="mb-3 col-lg-12 col-sm-12 d-flex">
                        <div class="w-auto flex-fill">
                            <div>
                                <label for="note_diagrams" class="form-label">Diagrams</label>
                            </div>
                            <select name="note_diagrams[]" id="note_diagrams" class="form-select" multiple="" >
                                <?php if($this->data && isset($this->data['note_diagrams']) && $this->data['note_diagrams']) :?>
                                    <?php foreach($this->data['note_diagrams'] as $note): ?>
                                        <option selected value="<?php echo $note['id'];?>"><?php echo $note['title'];?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-sm-6">
                <div class="row">
                    <div class="mb-3 col-lg-12 col-md-12 col-sm-12 d-flex">
                        <div class="w-auto flex-fill select-height">
                            <div>
                                <label for="note_description" class="form-label">Description</label>
                            </div>
                            <select name="note_description[]" id="note_description" class="form-select" >
                                <?php if($this->data && isset($this->data['note_description']) && $this->data['note_description']) :?>
                                    <?php foreach($this->data['note_description'] as $note): ?>
                                        <option selected value="<?php echo $note['id'];?>"><?php echo $note['title'];?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input class="form-control rounded-0 border border-1" type="hidden" name="_method" value="<?php echo $this->id ? 'PUT' : 'POST' ?>">
        <input type="hidden" name="save_close" id="save_close">
    </form>
</div>
<?php echo $this->render('backend.form.javascript');