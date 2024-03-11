<script>
    $(document).ready(function(){
        $(".popover-eye").popover({
            trigger: 'hover focus',
            html: true,
            placement: 'right'
        }); 
        $(".btn_apply").click(function(e) {
            e.preventDefault();
            $("#save_close").val(0);
            $('input#input_title').val($('input#title').val());
            if (!$('input#title').val()) {
                alert("Please enter a valid Title");
                $('html, body').animate({
                    scrollTop: 0
                });
                $('input#title').focus();
                return false;
            }
            $('#form_submit').submit();
        });
        $(".btn_save_close").click(function(e) {
            e.preventDefault();
            $("#save_close").val(1);
            $('input#input_title').val($('input#title').val());
            if (!$('input#title').val()) {
                alert("Please enter a valid Title");
                $('html, body').animate({
                    scrollTop: 0
                });
                $('input#title').focus();
                return false;
            }
            $('#form_submit').submit();
        });
        $('#clear_filter').on('click', function() {
            $("#milestone").val('').trigger('change');
            $("#tags").val('').trigger('change');
            $('input#input_title').val($('input#title').val());
            
            if (!$('input#title').val()) {
                alert("Please enter a valid Title");
                $('html, body').animate({
                    scrollTop: 0
                });
                $('input#title').focus();
                return false;
            }
            $('#form_submit').submit();
        });
        if (!$('th.today').length)
        {
            $('#active_today').attr('disabled', 'disabled');
        }

        $("#tags").select2({
            matcher: matchCustom,
            placeholder: 'All Tags',
            minimumInputLength: 1,
            multiple: true,
            closeOnSelect: false,
            ajax: {
                url: "<?php echo $this->link_tag ?>",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                    };
                },
                processResults: function(data, params) {
                    let items = [];
                    if (data.data.length > 0) {
                        data.data.forEach(function(item) {
                            items.push({
                                id: item.id,
                                text: item.name
                            })
                        })
                    }

                    return {
                        results: items,
                        pagination: {
                            more: false
                        }
                    };
                },
                cache: true
            },
        });

        function matchCustom(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }

            if (typeof data.text === 'undefined') {
                return null;
            }

            // Return `null` if the term should not be displayed
            return null;
        }
    });
    
</script>