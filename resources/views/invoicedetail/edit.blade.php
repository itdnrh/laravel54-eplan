@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            แก้ไขรายการขอสนับสนุน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">แก้ไขรายการขอสนับสนุน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="invoiceDetailCtrl"
        ng-init="initForms({
            departs: {{ $departs }},
            divisions: {{ $divisions }},
            invoice_item_detail: {{ $invoiceItemDetail }}
        });
        getById({{ $invoicedetail->ivd_id }}, setEditControls);
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">แก้ไขรายการขอสนับสนุน</h3>
                    </div>

                    <form id="frmEditInvoiceDetail" name="frmEditInvoiceDetail" method="post" action="{{ url('/invoicedetail/update/'.$invoicedetail->ivd_id) }}" role="form" enctype="multipart/form-data">
                        <input
                            type="hidden"
                            id="user"
                            name="user"
                            value="{{ Auth::user()->person_id }}"
                        />
                        <input
                            type="hidden"
                            id="duty_id"
                            name="duty_id"
                            value="{{ Auth::user()->memberOf->duty_id }}"
                            ng-model="invoiceDetail.duty_id"
                        />
                        <input
                            type="hidden"
                            id="depart_id"
                            name="depart_id"
                            value="{{ Auth::user()->memberOf->depart_id }}"
                            ng-model="invoiceDetail.depart_id"
                        />
                        <input
                            type="hidden"
                            id="division_id"
                            name="division_id"
                            value="{{ Auth::user()->memberOf->ward_id }}"
                            ng-model="invoiceDetail.division_id"
                        />
                        {{ csrf_field() }}

                        <div class="box-body">
                            <div class="row">
                                <div
                                    class="form-group col-md-2"
                                    ng-class="{'has-error has-feedback': checkValidate(invoiceDetail, 'year')}"
                                >
                                    <label>ปีงบประมาณ <span class="required-field">*</span> :</label>
                                    <select
                                        id="year"
                                        name="year"
                                        ng-model="invoiceDetail.year"
                                        class="form-control"
                                    >
                                        <option value="" disabled selected>--เลือกปี--</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>

                                    <span class="help-block" ng-show="checkValidate(invoiceDetail, 'year')">
                                        @{{ formError.errors.year[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-2"
                                    ng-class="{'has-error has-feedback': checkValidate(invoiceDetail, 'start_month')}"
                                >
                                    <label>ประจำเดือน : <span class="required-field">*</span></label>
                                    <select
                                        id="start_month"
                                        name="start_month"
                                        ng-model="invoiceDetail.start_month"
                                        class="form-control"
                                        tabindex="10"
                                    >
                                        <option value="" disabled selected>-- เลือกเดือน --</option>
                                        <option ng-repeat="month in monthLists" value="@{{ month.id }}">
                                            @{{ month.name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(invoiceDetail, 'start_month')">
                                        @{{ formError.errors.start_month[0] }}
                                    </span>
                                </div>
                             

                              <div 
                              class="form-group col-md-8"
                              ng-class="{'has-error has-feedback': checkValidate(invoiceDetail, 'ivh_id')}">
                                <!-- รายการ -->
                                <label>รายการ <span class="required-field">*</span> :</label>
                                <select 
                                name="ivh_id" 
                                id="ivh_id" 
                                ng-model="invoiceDetail.ivh_id" 
                                class="form-control"
                                ng-change="
                                onInvoiceDetailSelect(invoiceDetail.ivh_id);
                                "
                                >
                                  <option value="" selected disabled>-- ประเภทบิล --</option>
                                    @foreach($invoicehead as $ivh)
                                    <option value="{{ $ivh->ivh_id }}">
                                        เลขที่ : {{ $ivh->ivh_id }} ประเภทบิล : {{ $ivh->invoice_item_name }} - รายการ : {{ $ivh->invoice_detail_name }} - ยอดรวม : {{ number_format($ivh->sum_price,2) }}
                                    </option>
                                    @endforeach
                                </select>
                                <span class="help-block" ng-show="checkValidate(invoiceDetail, 'ivh_id')">
                                  @{{ formError.errors.ivh_id[0] }}
                                </span>
                              </div>

                              </div>
                                <!-- <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(invoiceDetail, 'invoice_item_id')}"
                                >
                                    <label>ประเภทบิล <span class="required-field">*</span> :</label>
                                    <select id="invoice_item_id"
                                            name="invoice_item_id"
                                            ng-model="invoiceDetail.invoice_item_id"
                                            ng-change="
                                                onInvoiceSelected(invoiceDetail.invoice_item_id);
                                                setPlanType(invoiceDetail.invoice_item_id);
                                                clearNewItem();
                                            "
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
                                        <option value="">-- ประเภทบิล --</option>
                                        @foreach($invoiceItem as $ivi)
                                            <option value="{{ $ivi->invoice_item_id }}">
                                                {{ $ivi->invoice_item_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(invoiceDetail, 'invoice_item_id')">
                                        @{{ formError.errors.invoice_item_id[0] }}
                                    </span>
                                </div> -->
                                <!-- <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(invoiceDetail, 'invoice_detail_id')}"
                                >
                                    <label>รายการบิล <span class="required-field">*</span> :</label>
                                    <select id="invoice_detail_id"
                                            name="invoice_detail_id"
                                            ng-model="invoiceDetail.invoice_detail_id"
                                            ng-change="
                                                setTopicByPlanType(invoiceDetail.invoice_detail_id);
                                                setCboCategory(invoiceDetail.invoice_detail_id);
                                                clearNewItem();
                                            "
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
                                        <option value="">-- รายการบิล --</option>
                                        <option ng-repeat="ivd in forms.invoice_item_detail" value="@{{ ivd.invoice_detail_id }}">
                                            @{{ ivd.invoice_detail_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(invoiceDetail, 'invoice_detail_id')">
                                        @{{ formError.errors.invoice_detail_id[0] }}
                                    </span>
                                </div>
                            </div> -->

                            <!-- <div class="row">
                              
                            </div> -->

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(invoiceDetail, 'use_price')}"
                                >
                                    <label>ยดดการใช้ (บาท) <span class="required-field">*</span> :</label>
                                    <input
                                        type="text"
                                        id="use_price"
                                        name="use_price"
                                        ng-model="invoiceDetail.use_price"
                                        class="form-control"
                                        ng-change="calculateSumPrice(invoiceDetail.use_price, invoiceDetail.remain_price)"
                                    />
                                    <span class="help-block" ng-show="checkValidate(invoiceDetail, 'use_price')">
                                        @{{ formError.errors.use_price[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(invoiceDetail, 'remain_price')}"
                                >
                                    <label>ยอดคงเหลือ (บาท) <span class="required-field">*</span> :</label>
                                    <input
                                        type="text"
                                        id="remain_price"
                                        name="remain_price"
                                        ng-model="invoiceDetail.remain_price"
                                        class="form-control"
                                    />
                                    <span class="help-block" ng-show="checkValidate(invoiceDetail, 'remain_price')">
                                        @{{ formError.errors.topic[0] }}
                                    </span>
                                </div>

                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(invoiceDetail, 'reason')}"
                                >
                                    <label>เหตุผลการดำเนินงาน <span class="required-field">*</span> :</label>
                                    <textarea
                                        rows="3"
                                        id="reason"
                                        name="reason"
                                        ng-model="invoiceDetail.reason"
                                        class="form-control"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(invoiceDetail, 'reason')">
                                        @{{ formError.errors.reason[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(invoiceDetail, 'detail')}"
                                >
                                    <label>รายละเอียดย่อย <span class="required-field">*</span> :</label>
                                    <textarea
                                        rows="3"
                                        id="detail"
                                        name="detail"
                                        ng-model="invoiceDetail.detail"
                                        class="form-control"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(invoiceDetail, 'detail')">
                                        @{{ formError.errors.detail[0] }}
                                    </span>
                                </div>
                            </div>
                            

                           

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(invoiceDetail, 'remark')}"
                                >
                                    <label>หมายเหตุ :</label>
                                    <input
                                        type="text"
                                        id="remark"
                                        name="remark"
                                        ng-model="invoiceDetail.remark"
                                        class="form-control"
                                        tabindex="1"
                                    />
                                    <span class="help-block" ng-show="checkValidate(invoiceDetail, 'remark')">
                                        @{{ formError.errors.remark[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(invoiceDetail, 'contact_person')}"
                                    ng-show="{{ Auth::user()->person_id }} == '1300200009261'"
                                >
                                    <label>ผู้ประสานงาน <span class="required-field">*</span> :</label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            id="contact_detail"
                                            name="contact_detail"
                                            class="form-control"
                                            ng-model="invoiceDetail.contact_detail"
                                            readonly
                                        />
                                        <input
                                            type="hidden"
                                            id="contact_person"
                                            name="contact_person"
                                            class="form-control"
                                            ng-model="invoiceDetail.contact_person"
                                        />
                                        <span class="input-group-btn">
                                            <button
                                                type="button"
                                                class="btn btn-info btn-flat"
                                                ng-click="showPersonList(4)"
                                            >
                                                ...
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(invoiceDetail, 'contact_person')">
                                        @{{ formError.errors.contact_person[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(invoiceDetail, 'head_of_depart')}"
                                    ng-show="{{ Auth::user()->person_id }} == '1300200009261'"
                                >
                                    <label>หัวหน้ากลุ่มงาน :</label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            id="head_of_depart_detail"
                                            name="head_of_depart_detail"
                                            class="form-control"
                                            ng-model="invoiceDetail.head_of_depart_detail"
                                            readonly
                                        />
                                        <input
                                            type="hidden"
                                            id="head_of_depart"
                                            name="head_of_depart"
                                            class="form-control"
                                            ng-model="invoiceDetail.head_of_depart"
                                        />
                                        <span class="input-group-btn">
                                            <button
                                                type="button"
                                                class="btn btn-info btn-flat"
                                                ng-click="showPersonList(5)"
                                            >
                                                ...
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(invoiceDetail, 'head_of_depart')">
                                        @{{ formError.errors.head_of_depart[0] }}
                                    </span>
                                </div>
                                <div class="col-md-6" ng-show="{{ Auth::user()->person_id }} != '1300200009261'"></div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'head_of_faction')}"
                                    ng-show="{{ Auth::user()->person_id }} == '1300200009261' || {{ Auth::user()->memberOf->depart_id }} == '27'"
                                >
                                    <label>หัวหน้ากลุ่มภารกิจ :</label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            id="head_of_faction_detail"
                                            name="head_of_faction_detail"
                                            class="form-control"
                                            ng-model="invoiceDetail.head_of_faction_detail"
                                            readonly
                                        />
                                        <input
                                            type="hidden"
                                            id="head_of_faction"
                                            name="head_of_faction"
                                            class="form-control"
                                            ng-model="invoiceDetail.head_of_faction"
                                        />
                                        <span class="input-group-btn">
                                            <button
                                                type="button"
                                                class="btn btn-info btn-flat"
                                                ng-click="showPersonList(6)"
                                            >
                                                ...
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(invoiceDetail, 'head_of_faction')">
                                        @{{ formError.errors.head_of_faction[0] }}
                                    </span>
                                </div>
                            </div>
                        </div><!-- /.box-body -->

                        <div class="box-footer clearfix">
                            <button
                               ng-click="formValidate($event, '/invoicedetail/validate', invoiceDetail, 'frmEditInvoiceDetail', update)"
                                class="btn btn-warning pull-right"
                            >
                                บันทึกการแก้ไข
                            </button>
                        </div><!-- /.box-footer -->
                    </form>

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('supports._plans-list')
        @include('supports._plan-groups-list')
        @include('supports._spec-form')
        @include('supports._subitems-list')
        @include('shared._persons-list')
        @include('shared._addons-list')

    </section>

    <script>
        $(function () {
            $('.select2').select2({
              templateResult: function (data) {
                if (!data.id) {
                  return data.text; // Return placeholder for the search field
                }

                // Get the current search term from Select2
                var searchTerm = $('.select2-search__field').val();

                // Replace '-' with new line using <br/>
                var formattedText = data.text.replace('-', '<br/>');

                // Highlight search term if present
                if (searchTerm) {
                  // Escape special characters for the search term in regex
                  var regex = new RegExp('(' + searchTerm + ')', 'gi');
                  formattedText = formattedText.replace(regex, '<strong>$1</strong>');
                }

                // Return the formatted HTML with new lines and highlighted search term
                return $('<span>' + formattedText + '</span>');
              },
              templateSelection: function (data) {
                return data.text; // Display selected option as is
              }
            });

            //$('#price_per_unit').inputmask("currency", { "placeholder": "0" });

            //$('#amount').inputmask("currency",{ "placeholder": "0", digits: 0 });

            $('#sum_price').inputmask("currency", { "placeholder": "0" });
            $('#use_price').inputmask("currency", { "placeholder": "0" });
            $('#remain_price').inputmask("currency", { "placeholder": "0" });
        });
    </script>
<style>
  /* Optional: Ensure the text wraps and new lines are displayed */
  .select2-results__option {
    white-space: normal;  /* Allow text to wrap to multiple lines */
  }
  
  /* Optional: Styling for highlighted text */
  strong {
    color: #ff5722;  /* Highlight color */
  }
</style>
@endsection

