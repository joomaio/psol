<?php
$this->theme->add($this->url . 'assets/timeline/css/style.css', '', 'timeline_style');
?>
<?php echo $this->renderWidget('core::notification', []); ?>
<div class="main min-height-auto">
    <main class="content p-0 ">
        <div class="container-fluid p-0">
            <div class="row justify-content-center mx-auto">
                <div class="col-12 p-0">
                    <div class="card border-0 shadow-none mb-0">
                        <div class="card-body pb-0">
                            <div class="row align-items-center">
                                <form id="form_submit" class="row pe-0 pb-2" action="<?php echo $this->link_form .'/'. $this->id ?>" method="POST">
                                    <div class="col-lg-11 col-sm-12">
                                        <input id="input_title" type="hidden" name="title">
                                        <input type="hidden" name="save_close" id="save_close">
                                        <div class="d-flex input-group-navbar">
                                            <div class="pe-2">
                                                <?php $this->ui->field('milestone');  ?>
                                            </div>
                                            <div class="pe-2">
                                                <select name="tags[]" id="tags" multiple>
                                                    <?php foreach($this->filter_tag as $tag): ?>
                                                        <option value="<?php echo $tag['id']?>" selected><?php echo $tag['name']?></option>
                                                    <?php endforeach;?>
                                                </select>
                                            </div>
                                            <button type='Submit' data-bs-toggle="tooltip" title="Filter" class="btn btn-outline-success btn_apply" type="button">
                                                Save
                                            </button>
                                            <button data-bs-toggle="tooltip" title="Clear Filter" id="clear_filter" class="btn btn-outline-secondary ms-2" type="button">
                                                Clear
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="_method" value="<?php echo $this->id ? 'PUT' : 'POST' ?>">
                                </form>
                            </div>
                            <div class="outline">
                                <table class="table-timeline">
                                    <thead>
                                    <tr>
                                            <th><span>Request</span> </th>
                                            <?php $tmp = $this->start_date;
                                            while ($tmp <= $this->end_date) { ?>
                                                <th class="<?php echo date('m-d-Y') == date('m-d-Y',$tmp) ? 'today' : ''; ?>">
                                                    <span>
                                                        <?php echo date('D', $tmp) ?><br><?php echo date('d-m', $tmp) ?>
                                                    </span>
                                                </th>
                                            <?php 
                                                $tmp += 24 * 60 * 60;
                                            } ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($this->data['requests'] as $index => $item) : ?>
                                        <tr>
                                            <th><a href="<?php echo $this->link_detail_request . '/'. $item['id'] ?>" target="_blank"><?php echo ($index +1) .'. '. $item['title'];?> </a>
                                                <a class="fa-solid fa-eye popover-eye"  data-bs-content="Assignments: <?php echo $item['assignment'] ? '<br>- '. htmlspecialchars(implode('<br>- ', $item['assignment'])) : '';  ?> 
                                                        <br><br>Status: <?php echo $item['status'] ? 'Done' : 'Not Done' ?>"
                                                        ></a>
                                                    <br/><small><?php echo htmlspecialchars(implode(' | ', $item['tags'])) ?></small>
                                            </th>
                                            <?php $tmp = $this->start_date;
                                            while ($tmp <= $this->end_date) { ?>
                                            <td class="position-relative <?php echo date('m-d-Y') == date('m-d-Y',$tmp) ? 'today' : ''; ?>">
                                                <?php if (date('d-m-Y', $tmp) == date('d-m-Y', strtotime($item['start_at']))) :  ?>
                                                    <div class="item-timeline <?php echo $item['status'] ? 'item-done' : ''; ?>"
                                                        style="width: <?php 
                                                            if ($item['finished_at'] && $item['finished_at'] != '0000-00-00 00:00:00')
                                                            {
                                                                echo strtotime($item['finished_at']) <= $this->end_date ? (round((strtotime($item['finished_at']) - strtotime($item['start_at'])) / 86400 ) + 1) * 151 - 10  : (round(($this->end_date - strtotime($item['start_at'])) / 86400 ) + 1) * 150 - 10 ;
                                                            }
                                                            else
                                                            {
                                                                echo 140;
                                                            }
                                                        ?>px">
                                                        <?php 
                                                            echo date('d-m-Y', strtotime($item['start_at']));
                                                            if ($item['finished_at'] && $item['finished_at'] != '0000-00-00 00:00:00' && strtotime($item['finished_at']) <= $this->end_date && strtotime($item['start_at']) != strtotime($item['finished_at']))
                                                            {
                                                                echo ' - '. date('d-m-Y', strtotime($item['finished_at']));
                                                            }
                                                        ?>
                                                    </div>
                                                <? endif; ?>    
                                            </td>
                                            <?php 
                                                $tmp += 24 * 60 * 60;
                                            } ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </main>
</div>
<?php echo $this->render('backend.report.form.javascript') ?>