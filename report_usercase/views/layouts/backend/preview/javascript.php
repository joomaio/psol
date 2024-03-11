<script>
    $(document).ready(function(e) {
        setTimeout(function(){
            loadDiagram();
        }, 1000);
        
        $('#select-diagram').on('change', function()
        {
           loadDiagram(); 
        });

        $('.button-toggle-description').on('click', function(){
            var btnText = $(this).text();
            $('.description-column').toggleClass("d-none");
            $('.diagrams-column').toggleClass("col-lg-8");
            $('.diagrams-column').toggleClass("col-lg-12");

            if(btnText == '[ + ]') {
                $(this).text('[ - ]');
            } else {
                $(this).text('[ + ]');
            }
        })
    });

    function loadDiagram()
    {
        var selected = $('#select-diagram').val();
        $('.item-diagram').addClass('d-none');
        $('#item-diagram-' + selected).removeClass('d-none');
    }
</script>