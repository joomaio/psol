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

        var detail_link = '<?php echo $this->link_ajax .'/' .$this->id ?>';

        $('.calendar-action').on('click', function(){
            var form = new FormData();
            form.append('current_day', $('#current_day').val());
            var action = $(this).data('action');
            form.append('action', action);
            $('.calendar-action').attr('disabled', true);

            $.ajax({  
                type: 'POST',  
                url: detail_link, 
                data: form,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status == 'success')
                    {
                        $('#calendar .calendar-month').text(response.month);
                        if (response.data)
                        {
                            html = '';
                            $('#current_day').val(response.current_day);
                            response.data.forEach(function(item, index){
                                if (item.day == 'Sunday')
                                {
                                    html += `<tr class="days">`;
                                }

                                html += `<td class="day ${item.class}">
                                            <div class="date">${item.date}</div>`;
                                item.event.forEach(function(event){
                                    var title = event.status == 'start' || item.day == 'Sunday' ? event.title : '';
                                    html += `<div class="event ${event.status}">
                                                <div class="event-desc">
                                                    <a target="_blank" href="<?php echo  $this->link_request  . '/' ?>${event.id}" >${title}</a>
                                                </div>
                                            </div>`
                                });
                                html += `</td>`;
                                if (item.day == 'Saturday')
                                {
                                    html+= '</tr>';
                                }
                            });

                            $('#calendar #table-main').html(html);
                        }
                        $('.calendar-action').removeAttr('disabled');
                    }
                    else
                    {
                        alert(response.message);
                    }
                }
            });
        });
    });
    
</script>