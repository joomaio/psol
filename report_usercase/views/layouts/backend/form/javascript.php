<script>
    $(document).ready(function(e) {
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
            var structure = [];
            $('.item-tree').each(function(index){
                var level = $(this).data('level');
                var position = $(this).data('position');
                var parent = $(this).data('parent');
                var id = $(this).data('id');
                structure.push({
                    id : id,
                    position : position,
                    parent : parent,
                    level : level,
                });
                $('#structure').val(JSON.stringify(structure));
                $('#removes').val(JSON.stringify(removes));
            });
            $('#form_submit').submit();
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
            var structure = [];
            $('.item-tree').each(function(index){
                var level = $(this).data('level');
                var position = $(this).data('position');
                var parent = $(this).data('parent');
                var id = $(this).data('id');
                structure.push({
                    id : id,
                    position : position,
                    parent : parent,
                    level : level,
                });
                $('#structure').val(JSON.stringify(structure));
                $('#removes').val(JSON.stringify(removes));
            });
            $('#form_submit').submit();
        });

        $("#note_diagrams").select2({
            matcher: matchCustom,
            placeholder: 'Select Diagram',
            minimumInputLength: 1,
            multiple: true,
            closeOnSelect: false,
            ajax: {
                url: "<?php echo $this->link_search ?>",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        type: 'mermaidjs',
                    };
                },
                processResults: function(data, params) {
                    let items = [];
                    if (data.data.length > 0) {
                        data.data.forEach(function(item) {
                            items.push({
                                id: item.id,
                                text: item.title
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

        $("#note_description").select2({
            matcher: matchCustom,
            minimumInputLength: 1,
            closeOnSelect: false,
            ajax: {
                url: "<?php echo $this->link_search ?>",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        type: 'html',
                    };
                },
                processResults: function(data, params) {
                    let items = [];
                    if (data.data.length > 0) {
                        data.data.forEach(function(item) {
                            items.push({
                                id: item.id,
                                text: item.title
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

        loadDiagram();
        $('#select-diagram').on('change', function()
        {
           loadDiagram(); 
        });
    });

    function loadDiagram()
    {
        var selected = $('#select-diagram').val();
        $('.item-diagram').addClass('d-none');
        $('#item-diagram-' + selected).removeClass('d-none');
    }
</script>