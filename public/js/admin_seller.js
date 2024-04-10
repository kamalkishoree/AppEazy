$(document).ready(function() {
        var table;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });
        $(".all-vendor_check").click(function() {
            if ($(this).is(':checked')) {
                $("#action_vendor_button").css("display", "block");
                $('.single_vendor_check').prop('checked', true);
            } else {
                $("#action_vendor_button").css("display", "none");
                $('.single_vendor_check').prop('checked', false);
            }
        });
        $(document).on('change', '.single_vendor_check', function() {
            if ($('input:checkbox.single_vendor_check:checked').length > 0) {
                $("#action_vendor_button").css("display", "block");
            } else {
                $('.all-product_check').prop('checked', false);
                $("#action_vendor_button").css("display", "none");
            }
        });
        $('#action_vendor_button').click(function() {
            $('#action-vendor-modal').modal({
                keyboard: false
            });
        });
        $(document).on('click', '.submitVendorAction', function(e) {
            var CSRF_TOKEN = $("input[name=_token]").val();
            var action = $('#action_for').val();
            var vendor_id = [];
             $('.single_vendor_check:checked').each(function(i){
                vendor_id[i] = $(this).val();
            });
            if (vendor_id.length == 0) {

                $("#action-vendor-modal .close").click();
                return;
            }
            if(action == 0){
                return false;
            }
           console.log(updateVendorAll);
            $.ajax({
                type: "post",
                url: updateVendorAll,
                data: {_token: CSRF_TOKEN,vendor_id:vendor_id,action:action},
                success: function(resp) {
                    if (resp.status == 'success') {
                        $.NotificationApp.send("Success", resp.message, "top-right", "#5ba035",
                            "success");
                            setTimeout(function(){ location.reload(); }, 3000);
                    }
                },
                beforeSend: function() {
                    $(".loader_box").show();
                },
                complete: function() {
                    $(".loader_box").hide();
                },
                error: function(response) {

                        $(".show_all_error.invalid-feedback").show();
                        $(".show_all_error.invalid-feedback").text(
                            'Something went wrong, Please try Again.');

                    return response;
                }
            });
        });

        setTimeout(function(){$('#active-vendor').trigger('click');}, 200);
        $(document).on('click', '#edit_vendor_modal #update_vendor_modal', function(e) {
            e.preventDefault();
            let myForm = document.getElementById('update_vendor_form');
            let formData = new FormData(myForm);
            var vendor_id = $("#edit_vendor_modal input[name='vendor_id']").val();
            var vendor_update_url =  base_url+'/client/vendor/'+vendor_id;
            $.ajax({
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                url: vendor_update_url,
                headers: {Accept: "application/json"},
                success: function(response) {
                    if (response.status == 'success') {
                        $("#edit_vendor_modal .close").click();
                        $.NotificationApp.send("Success", response.message, "top-right", "#5ba035", "success");
                        setTimeout(function(){location.reload();}, 2500);
                    } else {
                        $(".show_all_error.invalid-feedback").show();
                        $(".show_all_error.invalid-feedback").text(response.message);
                    }
                    return response;
                },
                beforeSend: function(){
                    $(".loader_box").show();
                },
                complete: function(){
                    $(".loader_box").hide();
                },
                error: function(response) {
                    if (response.status === 422) {
                        let errors = response.responseJSON.errors;
                        Object.keys(errors).forEach(function(key) {
                            $("#" + key + "Input input").addClass("is-invalid");
                            $("#" + key + "Input span.invalid-feedback").children("strong").text(errors[key][0]);
                            $("#" + key + "Input span.invalid-feedback").show();
                        });
                    } else {
                        $(".show_all_error.invalid-feedback").show();
                        $(".show_all_error.invalid-feedback").text('Something went wrong, Please try Again.');
                    }
                    return response;
                }
            });
        });
        $(document).on("click",".edit_vendor",function() {
            var vendor_id = $(this).data('vendor_id');
            $.ajax({
                data: '',
                type: "get",
                dataType: 'json',
                url: base_url+"/client/vendor/"+vendor_id+"/edit",
                success: function (data) {
                    $('#edit_vendor_modal').modal('show');
                    $('.selectize-select').selectize();
                    $('#edit_vendor_modal #editVendorBox').html(data.html);
                    // dine = document.getElementsByClassName('dine_in');
                    // var switchery = new Switchery(dine[0]);
                    // take = document.getElementsByClassName('takeaway');
                    // var switchery = new Switchery(take[0]);
                    // delivery = document.getElementsByClassName('delivery');
                    // var switchery = new Switchery(delivery[0]);
                    var elems = document.querySelectorAll('.editSwitchery');
                    elems.forEach(function(html) {
                        var switchery = new Switchery(html);
                    });
                    autocompletesWraps.push('edit');
                    loadMap(autocompletesWraps);
                    $('.dropify').dropify();
                    var input = document.querySelector("#editVendorBox #vendor_phone_number");
                    console.log(input); 
                    if(input){
                        window.intlTelInput(input, {
                            separateDialCode: true,
                            hiddenInput: "contact",
                            utilsScript: "{{asset('assets/js/utils.js')}}",
                            initialCountry: "",
                        });
                    }
                }
            });
        });
        $(document).on("click",".nav-link",function() {
            let rel= $(this).data('rel');
            let status= $(this).data('status');
            initDataTable(rel, status);
        });
        $(document).on("click",".delete-vendor",function() {
            var destroy_url = $(this).data('destroy_url');
            var id = $(this).data('rel');
            Swal.fire({
                title: "Are you sure?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Ok',
            }).then((result) => {
                if(result.value)
                {
                    $.ajax({
                        type: "POST",
                        dataType: 'json',
                        url: destroy_url,
                        data:{'_method':'DELETE'},
                        success: function(response) {
                            if (response.status == "Success") {
                                $.NotificationApp.send("Success", response.message, "top-right", "#5ba035", "success");
                                window.location.reload();
                            }
                        }
                    });
                }
            });
        });
        function initDataTable(table, status) {
            $('#'+table).DataTable({
                "destroy": true,
                "scrollX": true,
                "searching":true,
                "processing": true,
                "serverSide": true,
                "iDisplayLength": 20,
                "dom": '<"toolbar">Bftrip',
                language: {
                    search: "",
                    info: table_info,
                    paginate: { previous: "<i class='mdi mdi-chevron-left'>", next: "<i class='mdi mdi-chevron-right'>" },
                    searchPlaceholder: search_text
                },
                drawCallback: function () {
                    $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                },
                buttons: [],
                ajax: {
                  url: base_url+'/client/seller/filterdata',
                  complete: function(){
                    $('.vendor-products').removeClass('invisible');
                  },
                  data: function (d) {
                    d.status = status;
                    // d.search = $('input[type="search"]').val();
                    d.search =$('#'+table).DataTable().search();
                    d.date_filter = $('#range-datepicker').val();
                    d.payment_option = $('#payment_option_select_box option:selected').val();
                    d.tax_type_filter = $('#tax_type_select_box option:selected').val();
                  }
                },
                columns: dataTableColumn(status),
            });
        }

       

        function dataTableColumn(status){
         console.log(status);
            if(status == 1){
                return [
                    {data: 'checkbox',name: 'checkbox', orderable: false, searchable: false},
                    {data: 'order_number', name: 'order_number', orderable: false, searchable: false,"mRender": function ( data, type, full ) {
                        return "<a class='round_img_box' href='"+full.show_url+"'><img class='rounded-circle' src='"+full.logo.proxy_url+'90/90'+full.logo.image_path+"' alt='"+full.id+"'></a>";
                    }},
                    {data: 'name', name: 'name', orderable: false, searchable: false, "mRender": function ( data, type, full ) {
                        return "<a href='"+full.show_url+"'>"+full.name+"</a> ";
                    }},
                    {data: 'show_slot', name: 'show_slot', orderable: false, searchable: false, "mRender":function(data, type, full){
                        return "<span class='badge bg-soft-"+full.show_slot_label+" text-"+full.show_slot_label+"'>"+full.show_slot_option+"</span> | <a class='action-icon edit_vendor' href='javascript:void(0)' data-vendor_id='"+full.id+"'><i class='mdi mdi-square-edit-outline'></i></a>";
                    }},
                    {data: 'address', name: 'address', class:'address_txt',orderable: false, searchable: false, "mRender":function(data, type, full){
                        return "<p class='ellips_txt' data-toggle='tooltip' data-placement='top' title='"+full.address+"'>"+full.address+"</p>";
                    }},
                    {data: 'offers', name: 'offers', class:'text-center', orderable: false, searchable: false, "mRender":function(data, type, full){
                        var markup = '';
                        for (var i = full.offers.length - 1; i >= 0; i--) {
                            if(full.offers[i]){
                                markup+="<span class='badge bg-soft-warning text-warning'>"+full.offers[i]+"</span>";
                            }
                        }
                        return markup;
                    }},
                    {data: 'add_category_option', class:'text-center', name: 'add_category_option', orderable: false, searchable: false},
                    {data: 'commission_percent', class:'text-center', name: 'commission_percent', orderable: false, searchable: false},
                    {data: 'products_count', class:'text-center', class:'text-center', name: 'products_count', orderable: false, searchable: false},
                    {data: 'orders_count', class:'text-center', name: 'orders_count', orderable: false, searchable: false},
                    {data: 'currently_working_orders_count', class:'text-center', name: 'currently_working_orders_count', orderable: false, searchable: false},
                    {data: 'edit_action', class:'text-center', name: 'edit_action', orderable: false, searchable: false, "mRender":function(data, type, full){
                        if(status == 2){
                            return "<div class='form-ul'><div class='inner-div d-inline-block'><a class='action-icon' userId='"+full.id+"' href='"+full.show_url+"'><i class='mdi mdi-eye'></i></a></div></div>"
                        }else{
                            return "<div class='form-ul'><div class='inner-div d-inline-block'><a class='action-icon' userId='"+full.id+"' href='"+full.show_url+"'><i class='mdi mdi-eye'></i></a></div><div class='inner-div d-inline-block'><form method='POST' action='"+full.destroy_url+"'><div class='form-group action-icon mb-0'><button type='button' class='btn btn-primary-outline action-icon delete-vendor' data-destroy_url='"+full.destroy_url+"' data-rel='"+full.id+"'><i class='mdi mdi-delete'></i></button></div></form></div></div>"
                        }
                    }},
                ];
            }else{
                return  [
                    {data: 'order_number', name: 'order_number', orderable: false, searchable: false,"mRender": function ( data, type, full ) {
                        return "<a class='round_img_box' href='"+full.show_url+"'><img class='rounded-circle' src='"+full.logo.proxy_url+'90/90'+full.logo.image_path+"' alt='"+full.id+"'></a>";
                    }},
                    {data: 'name', name: 'name', orderable: false, searchable: false, "mRender": function ( data, type, full ) {
                        return "<a href='"+full.show_url+"'>"+full.name+"</a> ";
                    }},
                    {data: 'show_slot', name: 'show_slot', orderable: false, searchable: false, "mRender":function(data, type, full){
                        return "<span class='badge bg-soft-"+full.show_slot_label+" text-"+full.show_slot_label+"'>"+full.show_slot_option+"</span> | <a class='action-icon edit_vendor' href='javascript:void(0)' data-vendor_id='"+full.id+"'><i class='mdi mdi-square-edit-outline'></i></a>";
                    }},
                    {data: 'address', name: 'address', class:'address_txt',orderable: false, searchable: false, "mRender":function(data, type, full){
                        return "<p class='ellips_txt' data-toggle='tooltip' data-placement='top' title='"+full.address+"'>"+full.address+"</p>";
                    }},
                    {data: 'offers', name: 'offers', class:'text-center', orderable: false, searchable: false, "mRender":function(data, type, full){
                        var markup = '';
                        for (var i = full.offers.length - 1; i >= 0; i--) {
                            if(full.offers[i]){
                                markup+="<span class='badge bg-soft-warning text-warning'>"+full.offers[i]+"</span>";
                            }
                        }
                        return markup;
                    }},
                    {data: 'add_category_option', class:'text-center', name: 'add_category_option', orderable: false, searchable: false},
                    {data: 'commission_percent', class:'text-center', name: 'commission_percent', orderable: false, searchable: false},
                    {data: 'products_count', class:'text-center', class:'text-center', name: 'products_count', orderable: false, searchable: false},
                    {data: 'orders_count', class:'text-center', name: 'orders_count', orderable: false, searchable: false},
                    {data: 'currently_working_orders_count', class:'text-center', name: 'currently_working_orders_count', orderable: false, searchable: false},
                    {data: 'edit_action', class:'text-center', name: 'edit_action', orderable: false, searchable: false, "mRender":function(data, type, full){
                        if(status == 2){
                            return "<div class='form-ul'><div class='inner-div d-inline-block'><a class='action-icon' userId='"+full.id+"' href='"+full.show_url+"'><i class='mdi mdi-eye'></i></a></div></div>"
                        }else{
                            return "<div class='form-ul'><div class='inner-div d-inline-block'><a class='action-icon' userId='"+full.id+"' href='"+full.show_url+"'><i class='mdi mdi-eye'></i></a></div><div class='inner-div d-inline-block'><form method='POST' action='"+full.destroy_url+"'><div class='form-group action-icon mb-0'><button type='button' class='btn btn-primary-outline action-icon delete-vendor' data-destroy_url='"+full.destroy_url+"' data-rel='"+full.id+"'><i class='mdi mdi-delete'></i></button></div></form></div></div>"
                        }
                    }},
                ]
            }
        }
    });
