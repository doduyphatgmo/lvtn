
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"></h3>
                <div class="pull-left">
                    <?php $show = 0; ?>
                    @foreach($grid->rows() as $row)
                            @foreach($grid->columnNames as $name)
                                @if($name == 'Sô tín chỉ hiện tại' && $show == 0)
                                <?php $show++; ?>
                                    Sô TC hiện tại: {!! $row->column($name) !!}
                                @endif
                            @endforeach
                    @endforeach
                </div>
                <div class="pull-right">
                    {!! $grid->renderFilter() !!}
                    {!! $grid->renderExportButton() !!}
                    {!! $grid->renderCreateButton() !!}

                </div>

                <span>
{{--                    {!! $grid->renderHeaderTools() !!}--}}

                </span>

            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding ">
                <table class="table table-hover table-striped table-bordered tableColor">
                    <tr >
                        @foreach($grid->columns() as $column)
                            @if($column->getLabel() != 'Sô tín chỉ hiện tại')
                        <th class="colorth" style="background-color: #3c8dbc;color: white;"><b>{{$column->getLabel()}}{!! $column->sorter() !!}</b></th>
                            @endif
                        @endforeach
                    </tr>

                    @foreach($grid->rows() as $row)
                    <tr {!! $row->getRowAttributes() !!}>
                        @foreach($grid->columnNames as $name)
                            @if($name != 'Sô tín chỉ hiện tại')
                        <td {!! $row->getColumnAttributes($name) !!}>
                            {!! $row->column($name) !!}
                        </td>
                            @endif
                        @endforeach
                    </tr>
                    @endforeach

                    {!! $grid->renderFooter() !!}

                </table>
            </div>
            <div class="box-footer clearfix">
                {!! $grid->paginator() !!}
            </div>
            <!-- /.box-body -->
        </div>
