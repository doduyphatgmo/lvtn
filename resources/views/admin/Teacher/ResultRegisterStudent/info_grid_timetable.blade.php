<script>
$(function () {
    $('#resultRegisterStudent').change(function(e) {
            var selected = $( "#resultRegisterStudent" ).val();
            $.ajax({
                type:'GET',
                url:'/admin/teacher/infomation-register/'+selected+'/'+<?php echo $idUser; ?>,
                data:{_token: "{{ csrf_token() }}"
                },
                success: function( msg ) {
                    $('.gridResultAll').hide();
                    $('.gridTimeTable').hide();
                    $('.gridSubjectRegister').html(msg);
                    $(".grid-refresh").hide();
                    $("table").addClass('table-striped');
                    $("table").addClass('table-bordered');
                    $("th").css("background-color","#3c8dbc");
                    $("th").css("color","white");

                }
            });
            $.ajax({
                type:'GET',
                url:'/admin/teacher/infomation-timetable/'+selected+'/'+<?php echo $idUser; ?>,
                data:{_token: "{{ csrf_token() }}"
                },
                success: function( msg ) {
                    $('.time-table').html(msg);

                }
            });
    });
});
</script>
<div class="gridSubjectRegister">
</div>
<div class="time-table ">
</div>

