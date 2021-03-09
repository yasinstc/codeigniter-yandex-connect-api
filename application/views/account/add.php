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

    echo form_open( base_url('yandex_connect/add_account/'.$organization_id), [
        'class' => ''
    ]);

    echo form_hidden('organization_id', $organization_id);
    ?>
        <div class="form-group row">
            <div class="col-12">
                <label> New E-Mail (Username) </label>
                <div class="input-group">
                    <?php
                    echo form_input("nickname", set_value("nickname"), [
                        "class" => "form-control required"
                    ]);
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-6">
                <label> First Name </label>
                <?php
                echo form_input("name_first", set_value("name_first"), [
                    "class" => "form-control required"
                ]);
                ?>
            </div>

            <div class="col-6">
                <label> Last Name </label>
                <?php
                echo form_input("name_last", set_value("name_last"), [
                    "class" => "form-control required"
                ]);
                ?>
            </div>
        </div>

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
            echo form_submit("add_account", "Save", [
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