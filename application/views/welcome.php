<?php
$this->load->view('inc/header');
?>

<div class="col-12">
    <p> 1. <?php echo anchor( base_url('yandex_connect/list_applications'), 'Click Here'); ?> And Add New Application </p>
    <p> 2. Click "Get Access Token" Button And Get Access Token </p>
    <p> 3. Then Click "Organizations" Button </p>
    <p> 4. You Can Add, Delete, Edit Mail Addresses </p>
    <p> 5. That's It! </p>
</div>

<?php
$this->load->view('inc/footer');
?>