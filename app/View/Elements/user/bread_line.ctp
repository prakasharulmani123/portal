<div class="breadLine">
  <ul class="breadcrumb">
    <li><a href="#">Simple Admin</a> <span class="divider">></span></li>
    <li class="active">Dashboard</li>
  </ul>
  <ul class="buttons">
    <li> <a href="#" class="link_bcPopupList"><span class="icon-user"></span><span class="text">Users list</span></a>
      <div id="bcPopupList" class="popup">
        <div class="head">
          <div class="arrow"></div>
          <span class="isw-users"></span> <span class="name">List users</span>
          <div class="clear"></div>
        </div>
        <div class="body-fluid users">
          <div class="item">
            <div class="image"><a href="#"><?php echo $this->Html->image('admin/users/aqvatarius.jpg',array('width'=>'32')); ?></a></div>
            <div class="info"> <a href="#" class="name">Aqvatarius</a> <span>online</span> </div>
            <div class="clear"></div>
          </div>
          <div class="item">
            <div class="image"><a href="#"><?php echo $this->Html->image('admin/users/olga.jpg',array('width'=>'32')); ?></a></div>
            <div class="info"> <a href="#" class="name">Olga</a> <span>online</span> </div>
            <div class="clear"></div>
          </div>
          <div class="item">
            <div class="image"><a href="#"><?php echo $this->Html->image('admin/users/alexey.jpg',array('width'=>'32')); ?></a></div>
            <div class="info"> <a href="#" class="name">Alexey</a> <span>online</span> </div>
            <div class="clear"></div>
          </div>
          <div class="item">
            <div class="image"><a href="#"><?php echo $this->Html->image('admin/users/dmitry.jpg',array('width'=>'32')); ?></a></div>
            <div class="info"> <a href="#" class="name">Dmitry</a> <span>online</span> </div>
            <div class="clear"></div>
          </div>
          <div class="item">
            <div class="image"><a href="#"><?php echo $this->Html->image('admin/users/helen.jpg',array('width'=>'32')); ?></a></div>
            <div class="info"> <a href="#" class="name">Helen</a> </div>
            <div class="clear"></div>
          </div>
          <div class="item">
            <div class="image"><a href="#"><?php echo $this->Html->image('admin/users/alexander.jpg',array('width'=>'32')); ?></a></div>
            <div class="info"> <a href="#" class="name">Alexander</a> </div>
            <div class="clear"></div>
          </div>
        </div>
        <div class="footer">
          <button class="btn" type="button">Add new</button>
          <button class="btn btn-danger link_bcPopupList" type="button">Close</button>
        </div>
      </div>
    </li>
    <li> <a href="#" class="link_bcPopupSearch"><span class="icon-search"></span><span class="text">Search</span></a>
      <div id="bcPopupSearch" class="popup">
        <div class="head">
          <div class="arrow"></div>
          <span class="isw-zoom"></span> <span class="name">Search</span>
          <div class="clear"></div>
        </div>
        <div class="body search">
          <input type="text" placeholder="Some text for search..." name="search"/>
        </div>
        <div class="footer">
          <button class="btn" type="button">Search</button>
          <button class="btn btn-danger link_bcPopupSearch" type="button">Close</button>
        </div>
      </div>
    </li>
  </ul>
</div>
