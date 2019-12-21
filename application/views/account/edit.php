<?php
$this->load->view('inc/header');
?>

<div class="col-12">

    <?php
    echo heading('Account Infos', 2, [
        'class' => 'mt-0 mb-3'
    ]);

    if( $this->session->flashdata('data') ):
        $sess_success = $this->session->flashdata('success');
        $sess_data = $this->session->flashdata('data');

        if($sess_success):
            echo '<div class="alert alert-success">' . $sess_data['msg'] . '</div>';
        else:
            echo '<div class="alert alert-danger">' . $sess_data['msg'] . '</div>';                
        endif;
    endif;

    echo form_open( base_url('yandex_connect/edit_account/'.$organization_id.'/'.$id), [
        'class' => ''
    ]);

    echo form_hidden('organization_id', $organization_id);
    echo form_hidden('id', $id);
    ?>
        <div class="form-group row">
            <div class="col-12">
                <label> Password </label>
                <?php 
                echo form_password("password", "", [
                    "class" => "form-control required"
                ]);
                ?>
            </div>
        </div>
        
        <div class="form-group row">
            <div class="col-12">
            <?php 
            echo form_submit("edit_account", "Update", [
                "class" => "btn btn-primary b"
            ]);
            ?>
            </div>
        </div>
    <?php
    echo form_close();
    ?>
</div>

<?php
$this->load->view('inc/footer');
?>