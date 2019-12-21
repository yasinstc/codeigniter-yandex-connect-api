<?php
$this->load->view('inc/header');
?>

<div class="col-12">
    <?php
    if($success):
        echo heading('List Of E-Mails', 2, [
            'class' => 'mt-0 mb-3'
        ]);

        echo anchor( base_url('yandex_connect/add_account/'.$organization_id), 'Add New Account', [
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
            ['data' => 'E-Mail'],
            ['data' => 'Name'],
            ['data' => 'Created'],
            ['data' => 'Is Admin?'],
            ['data' => 'Change Password'],
            ['data' => 'Delete']
        ];

        $this->table->set_heading($arr_th);

        if($result):
            foreach($result->result as $data):

                $id = $data->id;
                $name = $data->name;
                $email = $data->email;
                $created = $data->created;
                $is_robot = $data->is_robot;
                $is_dismissed = $data->is_dismissed;
                $is_admin = $data->is_admin;

                if($is_robot)
                    continue;

                $arr_td = [
                    ['data' => $email],
                    ['data' => $name->first.' '.$name->last],
                    ['data' => $created],
                    ['data' => $is_admin ? 'True' : 'False'],
                    ['data' => anchor( site_url('yandex_connect/edit_account/'.$organization_id.'/'.$id), 'Change Password', [
                        'class' => 'btn btn-sm btn-primary'
                    ])],
                    ['data' => anchor( site_url('yandex_connect/delete_account/'.$organization_id.'/'.$id), 'Delete', [
                        'class' => 'btn btn-sm btn-danger delete',
                        'onclick' => "if(! confirm('Are you sure?')) { return false; }"
                    ])]
                ];

                $this->table->add_row($arr_td);

            endforeach;
        else:
        endif;

        echo $this->table->generate();

    else:
    endif;
    ?>
</div>

<?php
$this->load->view('inc/footer');
?>