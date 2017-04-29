<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
       <?php  echo $this->Html->css(array('style'));
        echo $this->Html->css(array('style_error'));?>
               
        <div class="errorPage">        
        <p class="name">403</p>
        <p class="description">Forbidden</p>        
        <p><button class="btn btn-danger" onclick="document.location.href ='<?php echo $this->base; ?>/admin/users'">Back to main</button>
            <button class="btn btn-warning" onclick="history.back();">Previous page</button></p>
    </div>
<?php
exit;
?>