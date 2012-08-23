<?php
    if(!osc_is_admin_user_logged_in()) {
        die;
    }

    $applicantId = Params::getParam("people");

    $mjb = ModelJB::newInstance();
    $people = $mjb->getApplicant($applicantId);
    
    $file = $mjb->getCVFromApplicant($applicantId);
    ModelJB::newInstance()->changeSecret($file['pk_i_id']);
    $file = $mjb->getCVFromApplicant($applicantId);
    $notes = $mjb->getNotesFromApplicant($applicantId);
    
    $job = Item::newInstance()->findByPrimaryKey($people['fk_i_item_id']);
    
    
    if($people['b_read']==0) {
        ModelJB::newInstance()->changeRead($applicantId);
    }
    


?>
<script src="<?php echo osc_plugin_url(__FILE__); ?>js/rating/jquery.rating.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo osc_plugin_url(__FILE__); ?>js/rating/jquery.MetaData.js" type="text/javascript" language="javascript"></script>
<link href="<?php echo osc_plugin_url(__FILE__); ?>js/rating/jquery.rating.css" type="text/css" rel="stylesheet">
<script type="text/javascript">
    $(document).ready(function() {
        
        $("#dialog-note-delete").dialog({
                    autoOpen: false,
                    modal: true
                });
                
        $("#dialog-note-edit").dialog({
                    autoOpen: false,
                    modal: true
                });
                
        $("#applicant_status").change(function(){
            $.getJSON(
                "<?php echo osc_admin_base_url(true); ?>?page=ajax&action=custom&ajaxfile=<?php echo osc_plugin_folder(__FILE__);?>ajax.php&paction=applicant_status",
                {"applicantId" : <?php echo $applicantId; ?>, "status" : $("#applicant_status option:selected").attr("value")},
                function(data){}
            );
        });
                
        $('.auto-star').rating({
            callback: function(value, link, input){
                var data = value.split("_");
                $.getJSON(
                    "<?php echo osc_admin_base_url(true); ?>?page=ajax&action=custom&ajaxfile=<?php echo osc_plugin_folder(__FILE__);?>ajax.php&paction=rating",
                    {"applicantId" : data[0], "rating" : data[1]},
                    function(data){}
                );
            }
        });
    });
    
    function delete_note(note_id) {
        $("#note_id").attr("value", note_id);
        $("#dialog-note-delete").dialog('open');
    }
    
    function edit_note(note_id, note_text) {
        $("#note_edit_id").attr("value", note_id);
        $("#note_edit_text").attr("value", note_text);
        $("#note_action").attr("value", "edit_note");
        $("#dialog-note-edit").dialog('open');
    }
    
    function add_note() {
        $("#note_edit_id").attr("value", "");
        $("#note_edit_text").attr("value", "");
        $("#note_action").attr("value", "add_note");
        $("#dialog-note-edit").dialog('open');
    }
    
</script>
<div>
    <h1><?php echo @$people['s_name']; ?></h1>
    
</div>

<div id="dashboard_div">
    <h2><?php _e("Dashboard", "jobboard"); ?></h2>
    <h3><?php _e("Job applied for", "jobboard"); ?></h3>
    <div id="dashboard_job">
        <table class="table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th><?php _e("Job title", "jobboard"); ?></th>
                    <th><?php _e("Rating for job", "jobboard"); ?></th>
                    <th><?php _e("Workflow status", "jobboard"); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $job['s_title']; ?></td>
                    <td>
                        <?php for($k=1;$k<=5;$k++) {
                            echo '<input name="star'.$people['pk_i_id'].'" type="radio" class="auto-star required" value="'.$people['pk_i_id'].'_'.$k.'" title="'.$k.'" '.($k==$people['i_rating']?'checked="checked"':'').'/>';
                        } ?>
                    </td>
                    <td>
                        <select id="applicant_status" name="applicant_status">
                            <?php
                            $st_array = jobboard_status();
                            foreach($st_array as $k => $v) {
                                echo '<option value="'.$k.'" '.($k==$people['i_status']?'selected="selected"':'').'>'.$v.'</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="clear:both;"></div>

    <h3><?php _e("Resume", "jobboard"); ?></h3>
    
    <div id="dashboard_notes">
        <?php if(empty($file)) {
            _e("This applicant has not sumitted any resume", "jobboard");
        } else if(strtolower(substr($file['s_name'], -4))==".pdf") { ?>
        
        
        <iframe src="http://docs.google.com/viewer?embedded=true&url=<?php echo str_replace("localhost", "95.62.72.123", osc_plugin_url(__FILE__));?>download.php?data=<?php echo $applicantId; ?>|<?php echo $file['s_secret']; ?>"></iframe>
 

        <?php } else {
            _e("This applicant's resume could not be displayed but could be downloaded", "jobboard");
        ?>
        <a href="<?php echo osc_plugin_url(__FILE__);?>download.php?id=<?php echo $applicantId; ?>" id="download_pdf" ><?php _e("Download", "jobboard"); ?></a>
        <?php }; ?>
    </div>
    
    <div style="clear:both;"></div>

    <h3><?php _e("Notes", "jobboard"); ?> <span class="note_plus"><a id="add_note" href="javascript:add_note();"><?php _e("Add note", "jobboard"); ?></a></span></h3>

    <div id="dashboard_notes">
        <div id="notes_buttons">
            
        </div>
        <div id="nots_table_div">
            <?php if(count($notes)>0) { ?>
                <table class="table" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th><?php _e("Note", "jobboard"); ?></th>
                            <th><?php _e("Date", "jobboard"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($notes as $note) { ?>
                        <tr>
                            <td><a class="delete_note" href="javascript:delete_note(<?php echo $note['pk_i_id']; ?>);" ><?php _e("DELETE _change_", "jobboard"); ?></a></td>
                            <td><a class="edit_note" href="javascript:edit_note(<?php echo $note['pk_i_id']; ?>, '<?php echo osc_esc_js($note['s_text']); ?>');" ><?php _e("EDIT _change_", "jobboard"); ?></a></td>
                            <td><?php echo $note['s_text']; ?></td>
                            <td><?php echo $note['dt_date']; ?></td>
                        </tr>
                        <?php }; ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <?php _e("No notes have been added to this applicant", "jobboard"); ?>
            <?php }; ?>
        </div>
    </div>
    
    <div style="clear:both;"></div>

</div>


<form id="dialog-note-delete" method="post" action="<?php echo osc_admin_base_url(true); ?>" class="has-form-actions hide" title="<?php echo osc_esc_html(__('Delete note', 'jobboard')); ?>">
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>actions.php" />
    <input type="hidden" name="applicantId" value="<?php echo $applicantId; ?>" />
    <input type="hidden" name="paction" value="delete_note" />
    <input type="hidden" id="note_id" name="id" value="" />
    <div class="form-horizontal">
        <div class="form-row">
            <?php _e('Are you sure you want to delete this note?'); ?>
        </div>
        <div class="form-actions">
            <div class="wrapper">
            <a class="btn" href="javascript:void(0);" onclick="$('#dialog-note-delete').dialog('close', 'jobboard');"><?php _e('Cancel', 'jobboard'); ?></a>
            <input id="note-delete-submit" type="submit" value="<?php echo osc_esc_html( __('Delete', 'jobboard') ); ?>" class="btn btn-red" />
            </div>
        </div>
    </div>
</form>

<form id="dialog-note-edit" method="post" action="<?php echo osc_admin_base_url(true); ?>" class="has-form-actions hide" title="<?php echo osc_esc_html(__('Note', 'jobboard')); ?>">
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>actions.php" />
    <input type="hidden" name="applicantId" value="<?php echo $applicantId; ?>" />
    <input type="hidden" id="note_edit_id" name="id" value="" />
    <input type="hidden" id="note_action" name="paction" value="add_note" />
    <div class="form-horizontal">
        <div class="form-row">
            <?php _e('Note', 'jobboard'); ?>
        </div>
        <div class="form-row">
            <textarea id="note_edit_text" name="note_text"></textarea>
        </div>
        <div class="form-actions">
            <div class="wrapper">
            <a class="btn" href="javascript:void(0);" onclick="$('#dialog-note-edit').dialog('close', 'jobboard');"><?php _e('Cancel', 'jobboard'); ?></a>
            <input id="note-edit-submit" type="submit" value="<?php echo osc_esc_html( __('Ok', 'jobboard') ); ?>" class="btn btn-red" />
            </div>
        </div>
    </div>
</form>