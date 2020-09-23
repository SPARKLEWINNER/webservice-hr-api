<?php
defined('BASEPATH') or exit('No direct script access allowed');
if ($this->session->id) {
    $this->load->view('base/media/index');
}
?>
</div>