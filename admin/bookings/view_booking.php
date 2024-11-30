<?php
require_once('./../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT  b.*,concat(c.lastname,', ', c.firstname,' ',c.middlename) as client from `booking_list` b inner join client_list c on b.client_id = c.id where b.id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
        $qry2 = $conn->query("SELECT f.*, c.name as category from `facility_list` f inner join category_list c on f.category_id = c.id where f.id = '{$facility_id}' ");
        if($qry2->num_rows > 0){
            foreach($qry2->fetch_assoc() as $k => $v){
                if(!isset($$k))
                $$k=$v;
            }
        }
    }
}
?>
<style>
    #uni_modal .modal-footer{
        display:none
    }
</style>
<div class="container-fluid">
<fieldset class="border-bottom">
        <legend class="h5 text-muted"> Detalle de Cancha</legend>
        <dl>
            <dt class="">Codigo cancha</dt>
            <dd class="pl-4"><?= isset($facility_code) ? $facility_code : "" ?></dd>
            <dt class="">Nombre</dt>
            <dd class="pl-4"><?= isset($name) ? $name : "" ?></dd>
            <dt class="">Categoria</dt>
            <dd class="pl-4"><?= isset($category) ? $category : "" ?></dd>
        </dl>
    </fieldset>
    <div class="clear-fix my-2"></div>
    <fieldset class="bor">
        <legend class="h5 text-muted"> Detalle de Reserva</legend>
        <dl>
            <dt class="">Codigo Ref.</dt>
            <dd class="pl-4"><?= isset($ref_code) ? $ref_code : "" ?></dd>
            <dt class="">Horario</dt>
            <dd class="pl-4">
             <?php 
                    if($date_from == $date_to){
                        echo date("M d, Y", strtotime($date_from));
                    }else{
                        echo date("M d, Y", strtotime($date_from))." - ".date("M d, Y", strtotime($date_to));
                    }
                ?>
            </dd>
            <dt class="">Estado</dt>
            <dd class="pl-4">
                <?php 
                    switch($status){
                        case 0:
                            echo "<span class='badge badge-secondary bg-gradient-secondary px-3 rounded-pill'>Pendiente</span>";
                            break;
                        case 1:
                            echo "<span class='badge badge-primary bg-gradient-primary px-3 rounded-pill'>Confirmado</span>";
                            break;
                        case 2:
                            echo "<span class='badge badge-warning bg-gradient-success px-3 rounded-pill'>Hecho</span>";
                            break;
                        case 3:
                            echo "<span class='badge badge-danger bg-gradient-danger px-3 rounded-pill'>Cancelado</span>";
                            break;
                    }
                ?>
            </dd>
        </dl>
    </fieldset>
        
    <div class="clear-fix my-3"></div>
    <div class="text-right">
        <?php if(isset($status) && $status == 0 ): ?>
        <button class="btn btn-default btn-flat bg-gradient-primary update_booking" type="button" data-status='1'>Confirmar Reservar</button>
        <?php endif; ?>
        <?php if(isset($status) && $status == 1 ): ?>
        <button class="btn btn-default btn-flat bg-gradient-success update_booking" type="button" data-status='2'>Marcar como hecho</button>
        <?php endif; ?>
        <?php if(isset($status) && in_array($status, [0,1])): ?>
        <button class="btn btn-danger btn-flat bg-gradient-danger update_booking" type="button" data-status='3'>Cancelar reserva</button>
        <?php endif; ?>
        <button class="btn btn-dark btn-flat bg-gradient-dark" type="button" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
    </div>
</div>
<script>
    $(function(){
        $('.update_booking').click(function(){
            var action = "Update to Pending", status = $(this).attr('data-status');
            if(status == 1)
                action = "Confirm"
            else if(status == 2)
                action = "Mark as Done"
            else if(status == 3)
                action = "Cancel"
            _conf("Are you sure to "+action+" this facility booking [Ref. Code: <b><?= isset($ref_code) ? $ref_code : "" ?></b>]?", "update_booking",["<?= isset($id) ? $id : "" ?>",status])
        })
    })
    function update_booking($id,$status){
        start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=update_booking_status",
			method:"POST",
			data:{id: $id,status:$status},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
    }
</script>
