<script>
$(function () {
    $('#resultRegister').change(function(e) {
            var selected = $( "#resultRegister" ).val();

            $.ajax({
                type:'GET',
                url:'/user/register-result/'+selected,
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
                    $(".th-object:first-child").css("background-color","white");
                    $('h1').css({
                        'font-weight': 'bold',
                        'font-family': 'Times New Roman'
                    });
                    $('li').css({
                        'font-weight': 'bold',
                        'font-family': 'Times New Roman'
                    });
                }
            });
            $.ajax({
                type:'GET',
                url:'/user/timetable-result/'+selected,
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

