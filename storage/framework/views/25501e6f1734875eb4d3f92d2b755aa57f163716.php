<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <!-- /.box-header -->
            <!-- form start -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab-form-1" data-toggle="tab" aria-expanded="true">
                            Lớp <i class="fa fa-exclamation-circle text-red hide"></i>
                        </a>
                    </li>
                    
                        
                            
                        
                    
                </ul>
                <div class="tab-content fields-group">
                    <div class="tab-pane active" id="tab-form-1">
                        <?php echo $__env->make('admin.Department.table_class', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    </div>
                    
                        
                    
                </div>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>