<?php
$this->load->view('inc/header');
?>

<div class="col-12">

    <?php
    echo heading('Application Infos', 2, [
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

    echo form_open( isset($application) ?
        base_url('yandex_connect/edit_application/'.$application->id) :
        base_url('yandex_connect/add_application'), [
        'class' => ''
    ]);

    /**
     * If We Are Editing The Application
     */
    if(isset($application))
        echo form_hidden('id', $application->id);
    ?>
        <div class="form-group row">
            <div class="col-12">
                <label> App Name </label>
                <div class="input-group">
                    <?php
                    echo form_input("app_name", isset($application) ? $application->app_name : set_value("app_name"), [
                        "class" => "form-control"
                    ]);
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-6">
                <label> App ID </label>
                <?php
                echo form_input("app_id", isset($application) ? $application->app_id : set_value("app_id"), [
                    "class" => "form-control"
                ]);
                ?>
            </div>

            <div class="col-6">
                <label> App Secret </label>
                <?php
                echo form_input("app_secret", isset($application) ? $application->app_secret : set_value("app_secret"), [
                    "class" => "form-control"
                ]);
                ?>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-12">
            <?php
            echo form_submit("submit_application", "Save", [
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