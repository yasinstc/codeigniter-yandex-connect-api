<?php
$this->load->view('inc/header');
?>

<div class="col-12">
    <?php
    echo heading('List Of Applications', 2, [
        'class' => 'mt-0 mb-3'
    ]);

    echo anchor( base_url('yandex_connect/add_application/'), 'Add New Application', [
        'class' => 'btn btn-primary mb-3'
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

    $this->table->set_template([
        'table_open' => '<table class="table table-striped mt-3" width="100%">'
    ]);
    
    $arr_th = [
        ['data' => 'ID'],
        ['data' => 'App Name'],
        ['data' => 'Access Token'],
        ['data' => 'Expire Date'],
        ['data' => 'Get Access Token'],
        ['data' => 'Organizations'],
        ['data' => 'Edit'],
        ['data' => 'Delete']
    ];

    $this->table->set_heading($arr_th);

    if($applications):
        foreach($applications as $data):

            $id = $data->id;
            $app_name = $data->app_name;
            $access_token = $data->access_token;
            $expire_date = $data->expire_date;
            $btn_getAccessToken = anchor( base_url('yandex_connect/authorize/'.$id), 'Get Access Token', [
                'class' => 'btn btn-primary btn-sm'
            ]);
            $btn_Organizations = anchor( base_url('yandex_connect/list_organizations/'.$id), 'Organizations', [
                'class' => 'btn btn-secondary btn-sm'
            ]);            
            $btn_Edit = anchor( base_url('yandex_connect/edit_application/'.$id), 'Edit', [
                'class' => 'btn btn-info btn-sm'
            ]);
            $btn_Delete = anchor( base_url('yandex_connect/delete_application/'.$id), 'Delete', [
                'class' => 'btn btn-danger btn-sm',
                'onclick' => "if(! confirm('Are you sure?')) { return false; }"
            ]);

            $arr_td = [
                ['data' => $id],
                ['data' => $app_name],
                ['data' => $access_token],                
                ['data' => $expire_date],
                ['data' => $btn_getAccessToken],
                ['data' => $access_token ? $btn_Organizations : '—'],
                ['data' => $btn_Edit],
                ['data' => $btn_Delete]
            ];

            $this->table->add_row($arr_td);

        endforeach;
    endif;

    echo $this->table->generate();
    ?>
</div>

<?php
$this->load->view('inc/footer');
?>