<?php
$this->load->view('inc/header');
?>

<div class="col-12">
    <?php    
    echo heading('List Of Organizations', 2, [
        'class' => 'mt-0 mb-3'
    ]);

    if($success):
        $this->table->set_template([
            'table_open' => '<table class="table table-striped" width="100%">'
        ]);

        $this->table->set_heading([
            ['data' => 'ID', 'style' => 'width: 150px;'],
            ['data' => 'Domain'],
            ['data' => 'Details', 'style' => 'width: 75px;']
        ]);

        foreach($data['result'] as $org):
            $id = $org->id;
            $domains = $org->domains->all;
            $domains = implode(",", $domains);
            $details = anchor( base_url('yandex_connect/list_accounts/'.$id), 'Details', [
                'class' => 'btn btn-primary'
            ]);
            $this->table->add_row([
                ['data' => $id],
                ['data' => $domains],
                ['data' => $details]
            ]);
        endforeach;

        echo $this->table->generate();

    else:
        ?>
        <div class="alert alert-danger">
            <p class="mb-0"> Somethings is wrong. <?php echo $data->http_code.' '.$data->message; ?> </p>
        </div>
        <?php
    endif;
    ?>
</div>

<?php
$this->load->view('inc/footer');
?>