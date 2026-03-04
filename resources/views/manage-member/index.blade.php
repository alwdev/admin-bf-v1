@extends('layouts.guest')
@section('styles')
{{-- <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/select.bootstrap4.css')}}" rel="stylesheet" type="text/css" /> --}}
<style type="text/css">
    .bank-logo{
        margin-bottom: 1rem;
    }
</style>
@endsection
@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">{{__('managemember.Member_list')}}</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">/</a></li>
                    <li class="breadcrumb-item active">{{__('managemember.Member_list')}}</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row card">
    <div class="col-12">
        <div class="card-body">
            <h4 class="card-title"></h4>
            <p class="card-subtitle mb-4">
            </p>

                <table id="basic-datatable" class="table nowrap table-striped"
                data-filter-control="true"
                data-toggle="table"
                data-search="true"
                data-show-export="false"
                data-click-to-select="false"
                data-pagination="true"
                data-url="">
                    <thead>
                        <tr>
                            <th data-field="member_id" data-filter-control="input" data-sortable="true">{{__('managemember.user_code')}}</th>
                            <th data-field="username" data-filter-control="input" data-sortable="true">{{__('managemember.user_name')}}</th>
                            @if( json_decode(auth()->user()->permissions)->manageuser > 2  )
                            <th>{{__('managemember.password')}}</th>
                            @endif
                            <th data-field="fullname" data-filter-control="input" data-sortable="true">{{__('managemember.name_lastname')}}</th>
                            <th data-sortable="true">{{__('managemember.amount')}}</th>
                            <th>{{__('managemember.BankAccount')}}</th>
                            <th data-sortable="true">{{__('managemember.Registration_date')}}</th>
                            <th></th>
                             <th>{{ __('Affiliate') }}</th>
                            @if( json_decode(auth()->user()->permissions)->member > 2  )
                            <th>{{__('managemember.manage')}}</th>
                            @endif
                            <th>{{__('managemember.Edited_by')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($memberlist as $key => $member)
                        @if ($member->enable == 0)
                            <tr style="color: rgb(211, 88, 5);">
                        @else
                            <tr style="color: rgb(5, 5, 5);">
                        @endif

                            <td>{{ $member->member_id }}</td>
                            <td>{{ $member->username }}</td>
                            @if( json_decode(auth()->user()->permissions)->manageuser > 2  )
                            {{-- <td><button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="changePass('{{ $member->id }}')"><i class="bx bx-edit-alt"></i>เปลียน</button>
                            </td> --}}
                            <td class="text-right">
                                @if( json_decode(auth()->user()->permissions)->manageuser > 2  )
                                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light"  data-toggle="modal" data-target="#editPass{{ $key }}"><i class="bx bx-edit-alt"></i>{{__('managemember.change')}}</button>
                                <div class="modal fade" id="editPass{{ $key }}" tabindex="-1" aria-labelledby="editPass{{ $key }}Label" aria-hidden="true">
                                    <div class="modal-dialog  modal-dialog-centered modal-dialog-scrollable">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="editPass{{ $key }}Label">{{__('managemember.change_password')}}</h5>
                                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                          </button>
                                        </div>
                                        <form action="{{ route('managemember.changePassword') }}" method="POST" id="form_editPass{{ $key }}">
                                        <div class="modal-body">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $member->id }}" required>
                                            <div class="form-group  text-left">
                                                <label for="">{{__('managemember.Enter_a_new_password')}}</label>
                                                <input type="text" name="password" class="form-control" required>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                          <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                      </div>
                                    </div>
                                  </div>

                                @endif
                            </td>
                            @endif
                            <td>{{ $member->fullname }}</td>
                            @php
                                $member_balance = $member->wallet_balance;
                            @endphp
                            <td class="text-right">{{ $member_balance }} ฿
                                @if( json_decode(auth()->user()->permissions)->manageuser > 2  )
                                {{-- <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="editBalance('{{ $member->id }}','{{ $member->username }}','{{ Auth::user()->id }}')"><i class="bx bx-edit-alt"></i></button> --}}
                                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light"  data-toggle="modal" data-target="#editBalance{{ $key }}"><i class="bx bx-edit-alt"></i></button>
                                <div class="modal fade" id="editBalance{{ $key }}" tabindex="-1" aria-labelledby="editBalance{{ $key }}Label" aria-hidden="true">
                                    <div class="modal-dialog  modal-dialog-centered modal-dialog-scrollable">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="editBalance{{ $key }}Label">{{__('managemember.Edit_balance')}}</h5>
                                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                          </button>
                                        </div>
                                        <form action="{{ route('managemember.memberEditBalance') }}" method="POST" id="form_editBalance{{ $key }}">
                                        <div class="modal-body">
                                            @csrf
                                            <input type="hidden" name="member_id" value="{{ $member->id }}" required>
                                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" required>
                                            <div class="form-group  text-left">
                                                <label for="">{{__('managemember.amount')}}</label>
                                                <input type="text" name="balance" class="form-control" onkeypress="return isNumberKey(event)" required>
                                            </div>
                                            <div class="form-group text-left">
                                                <label for="">{{__('dashboard.type')}}</label>
                                                <select name="type" class="form-control" required>
                                                    <option value=""></option>
                                                    <option value="เติมมือ">{{__('dashboard.Add_normal')}}</option>
                                                    {{-- <option value="คืนลูกค้า">{{__('dashboard.Customer_Refund')}}</option> --}}
                                                    <option value="แก้เครดิต">{{__('managemember.Edit_balance')}}</option>
                                                </select>
                                            </div>


                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                          <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                      </div>
                                    </div>
                                  </div>

                                @endif
                            </td>
                            <td>
                                <button href="่javascript:void(0);" data-toggle="modal" data-target="#exampleModal{{ $key }}" type="button" class="btn btn-secondary  btn-sm waves-effect waves-light">{{__('managemember.BankAccount')}}</button>
                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal{{ $key }}" tabindex="-1" aria-labelledby="exampleModal{{ $key }}Label" aria-hidden="true">
                                    <div class="modal-dialog">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="exampleModal{{ $key }}Label">{{__('managemember.Account_details')}}</h5>
                                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                          </button>
                                        </div>
                                        <div class="modal-body text-center">
                                            @switch($member->bank_name)
                                            @case("ธนาคารกรุงเทพ")
                                                    <img src="{{ asset('images/bank/bbl.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารกสิกรไทย")
                                                    <img src="{{ asset('images/bank/kbank.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารกรุงไทย")
                                                    <img src="{{ asset('images/bank/ktb.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารทหารไทยธนชาต")
                                                    <img src="{{ asset('images/bank/ttb.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารไทยพาณิชย์")
                                                    <img src="{{ asset('images/bank/scb.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารกรุงศรีอยุธยา")
                                                    <img src="{{ asset('images/bank/bay.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารเกียรตินาคินภัทร")
                                                    <img src="{{ asset('images/bank/kk.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารซีไอเอ็มบีไทย")
                                                    <img src="{{ asset('images/bank/cimb.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารทิสโก้")
                                                    <img src="{{ asset('images/bank/tisco.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารยูโอบี")
                                                    <img src="{{ asset('images/bank/uob.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารไทยเครดิต")
                                                    <img src="{{ asset('images/bank/tcrb.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารออมสิน")
                                                    <img src="{{ asset('images/bank/gsb.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร")
                                                    <img src="{{ asset('images/bank/baac.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("askmepay")
                                                    <img src="{{ asset('images/bank/askmepay.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("TrueMoney Wallet")
                                                    <img src="{{ asset('images/bank/truemoney.png') }}" width="80" class="bank-logo">
                                                    @break
                                                      @case("ธนาคารการค้า")
                                                <img id="member_deposit_img_bank_logo" src="{{ asset('images/bank/bcel.jpg') }}"  width="80" class="bank-logo">
                                                @break
                                            @case("ธนาคารลาวพัฒนา")
                                                <img id="member_deposit_img_bank_logo" src="{{ asset('images/bank/trust.jpg') }}"  width="80" class="bank-logo">
                                                @break
                                            @case("ธนาคารJDB")
                                                <img id="member_deposit_img_bank_logo" src="{{ asset('images/bank/jdb.jpg') }}"  width="80" class="bank-logo">
                                                @break
                                                @default
                                                    <div style="width:40px;height:40px;background:#E3A941;"></div>
                                            @endswitch
                                            <br/>
                                            <h3>{{ $member->bank_name }}</h3>
                                            <h3>{{ $member->account_name }}</h3>
                                            <h3>{{ $member->bank_number }}</h3>
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                      </div>
                                    </div>
                                  </div>

                                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light"  data-toggle="modal" data-target="#updateBankAccount{{ $key }}"><i class="bx bx-edit-alt"></i></button>
                                <div class="modal fade" id="updateBankAccount{{ $key }}" tabindex="-1" aria-labelledby="updateBankAccount{{ $key }}Label" aria-hidden="true">
                                    <div class="modal-dialog  modal-dialog-centered modal-dialog-scrollable">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="updateBankAccount{{ $key }}Label">{{__('managemember.Edit_bank_account')}}</h5>
                                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                          </button>
                                        </div>
                                        <form action="{{ route('managemember.memberupdateBankAccount') }}" method="POST" id="form_updateBankAccount{{ $key }}">
                                        <div class="modal-body">
                                            @csrf
                                            <input type="hidden" name="member_id" value="{{ $member->id }}" required>
                                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" required>
                                            <div class="col-12  mb-3">
                                                <div class="form-group">
                                                    <input type="hidden" value="" name="bank_logo" id="bank_logo">
                                                    <select name="bank_name" id="bank_name" required class="form-control" onchange="$('#bank_code{{ $key }}').val(this.options[this.selectedIndex].getAttribute('code'))">
                                                        <option value="">{{__('managemember.Select_a_bank')}}</option>
                                                        <option code="true-wallet" value="TrueMoney Wallet" @if($member->bank_name == "TrueMoney Wallet") selected @endif>TrueMoney Wallet</option>
                                                        <option code="bank-1" value="ธนาคารกรุงเทพ" @if($member->bank_name == "ธนาคารกรุงเทพ") selected @endif>ธนาคารกรุงเทพ</option>
                                                        <option code="bank-0" value="ธนาคารกสิกรไทย" @if($member->bank_name == "ธนาคารกสิกรไทย") selected @endif>ธนาคารกสิกรไทย</option>
                                                        <option code="bank-2" value="ธนาคารกรุงไทย" @if($member->bank_name == "ธนาคารกรุงไทย") selected @endif>ธนาคารกรุงไทย</option>
                                                        <option code="bank-3" value="ธนาคารทหารไทยธนชาต" @if($member->bank_name == "ธนาคารทหารไทยธนชาต") selected @endif>ธนาคารทหารไทยธนชาต</option>
                                                        <option code="bank-4" value="ธนาคารไทยพาณิชย์" @if($member->bank_name == "ธนาคารไทยพาณิชย์") selected @endif>ธนาคารไทยพาณิชย์</option>
                                                        <option code="bank-10" value="ธนาคารกรุงศรีอยุธยา" @if($member->bank_name == "ธนาคารกรุงศรีอยุธยา") selected @endif>ธนาคารกรุงศรีอยุธยา</option>
                                                        <option code="bank-25" value="ธนาคารเกียรตินาคินภัทร" @if($member->bank_name == "ธนาคารเกียรตินาคินภัทร") selected @endif>ธนาคารเกียรตินาคินภัทร</option>
                                                        <option code="bank-8" value="ธนาคารซีไอเอ็มบีไทย" @if($member->bank_name == "ธนาคารซีไอเอ็มบีไทย") selected @endif>ธนาคารซีไอเอ็มบีไทย</option>
                                                        <option code="bank-24" value="ธนาคารทิสโก้" @if($member->bank_name == "ธนาคารทิสโก้") selected @endif>ธนาคารทิสโก้</option>
                                                        <option code="bank-9" value="ธนาคารยูโอบี" @if($member->bank_name == "ธนาคารยูโอบี") selected @endif>ธนาคารยูโอบี</option>
                                                        {{-- <option code="014" value="ธนาคารไทยเครดิต" @if($member->bank_name == "ธนาคารไทยเครดิต") selected @endif>{{ trans('register.CREDIT') }}</option> --}}
                                                        <option code="bank-11" value="ธนาคารออมสิน" @if($member->bank_name == "ธนาคารออมสิน") selected @endif>ธนาคารออมสิน</option>
                                                        <option code="bank-15" value="ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร" @if($member->bank_name == "ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร") selected @endif>ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร</option>
                                                        <option code="bank-16" value="ธนาคารการค้า" @if($member->bank_name == "ธนาคารการค้า") selected @endif>ธนาคารการค้า</option>
                                                        <option code="bank-17" value="ธนาคารลาวพัฒนา" @if($member->bank_name == "ธนาคารลาวพัฒนา") selected @endif>ธนาคารลาวพัฒนา</option>
                                                        <option code="bank-18" value="ธนาคารJDB" @if($member->bank_name == "ธนาคารJDB") selected @endif>ธนาคารJDB</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12  mb-3">
                                                <div class="form-group">
                                                    <label for="">{{__('managemember.Bank_Code')}}</label>
                                                    <input type="text" class="form-control w-100" id="bank_code{{ $key }}" name="bank_code" value="{{ $member->bank_code }}" readonly autofocus autocomplete="bank_code" required>
                                                </div>
                                            </div>
                                            <div class="col-12  mb-3">
                                                <div class="form-group">
                                                    <label for="">{{__('managemember.Account_number')}}</label>
                                                    <input type="text" class="form-control w-100" name="bank_number" value="{{ $member->bank_number }}"  onkeypress="return isNumber(event)" autofocus autocomplete="bank_number" required>
                                                </div>
                                            </div>
                                            <div class="col-12  mb-3">
                                                <div class="form-group">
                                                    <label for="">{{__('managemember.BankAccount')}}</label>
                                                    <input type="text" class="form-control w-100" name="account_name"  value="{{ $member->account_name }}" autofocus autocomplete="account_name" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                          <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                      </div>
                                    </div>
                                  </div>
                            </td>
                            <td>{{ $member->created_at->format('d/m/Y H:i:s') }}</td>
                            <td><a href="{{ route('managemember.historyTransfer',$member->id) }}" type="button" class="btn btn-success  btn-sm waves-effect waves-light"><i class="bx bx-bitcoin"></i> {{__('managemember.finance')}}</a></td>
                             <td>
                                    <a href="{{ route('managemember.affiliates', ['id' => $member->id]) }}"
                                        class="btn btn-primary btn-sm waves-effect waves-light">
                                        <i class="bx bx-group"></i>
                                        {{ is_null(json_decode($member->ref_user, true)) ? 0 : count(json_decode($member->ref_user, true)) }}
                                    </a>
                                </td>
                            @if( json_decode(auth()->user()->permissions)->member > 2  )
                            <td>
                                <button type="button" class="btn btn-warning btn-sm waves-effect waves-light" onclick="lock('{{ $member->id }}','{{ Auth::user()->id }}','{{ $member->username }}','{{ $member->enable }}')" style="width: 80px;"><i class="bx bx-edit-alt" ></i>{{ $member->enable == 1 ? __('managemember.lock') : __('managemember.unlock')  }}</button>
                                <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="delete_member('{{ $member->id }}','{{ Auth::user()->id }}','{{ $member->username }}')" style="width: 80px;"><i class="bx bx-edit-alt"></i>{{__('managemember.delete')}}</button>
                            </td>
                            @endif
                            <td>{{ App\Http\Controllers\ManageMemberController::staff_detail($member->update_by) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->
@endsection
@section('scripts')

    <!-- third party js -->
    {{-- <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.js')}}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.buttons.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/buttons.html5.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/buttons.flash.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/buttons.print.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.keyTable.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.select.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/pdfmake.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/vfs_fonts.js')}}"></script> --}}
    <!-- third party js ends -->

    <!-- Datatables init -->
    {{-- <script src="{{ asset('pages/datatables-demo.js')}}"></script> --}}
    <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables/responsive.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables/buttons.bootstrap4.css') }}">
    <link href="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.css" rel="stylesheet">

    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>


    <script>

    function editBalance(member_id,member_name,user_id) {
        Swal.mixin({
                input: 'text',
                confirmButtonText: '{{__('main.yes')}} &rarr;',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                progressSteps: ['1', '2']
            }).queue([
                {
                title: '{{__('managemember.Edit_balance')}}',
                text: '{{__('managemember.Enter_the_amount')}}',
                }
            ]).then( function (result) {
                if (result.value) {
                        $.ajax({
                            type: 'post',
                            headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: '{{ route('managemember.memberEditBalance') }}',
                            data: { member_id:member_id,user_id:user_id,balance:Number(result.value) },
                            success: function (data) {
                                if(data!=false){
                                    Swal.fire(
                                            {
                                                title: '{{__('managemember.Edit_balance')}}',
                                                html: '<h4>{{__('managemember.user_name')}}: '+member_name+' </br></h4>' +
                                                '<h4>success</br></h4>',
                                                type: 'success',
                                                confirmButtonText: '{{__('main.yes')}}',
                                                confirmButtonClass: 'btn btn-confirm mt-2'

                                            }
                                        ).then(function() {
                                            location.reload();
                                        });

                                    }
                                }
                        });
                }else if(result.dismiss == 'cancel'){
                    console.log('cancel');
                    }
            })
    }
    function changePass(member_id) {
        Swal.mixin({
            // input: 'text',
            confirmButtonText: '{{__('main.yes')}} &rarr;',
            showCancelButton: true,
            cancelButtonText: '{{__('main.no')}}',
            progressSteps: ['1', '2']
        }).queue([
            {
            title: '{{__('managemember.Change_password')}}',
            text: '',
            }
        ]).then( function (result) {
            if (result.value) {
                    $.ajax({
                        type: 'post',
                         headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                          },
                          url: '{{ route('managemember.changePassword') }}',
                          data: { id:member_id },
                          success: function (data) {
                            if(data!=false){
                                Swal.fire(
                                        {
                                            title: '{{__('managemember.Change_password')}}',
                                            html: '<h4>{{__('managemember.user_name')}}: '+data[0].username+'</br></h4>' +
                                            '<h4>{{__('managemember.password')}}: '+data[1]+'</br></h4>',
                                            type: 'success',
                                            confirmButtonText: '{{__('main.yes')}}',
                                            confirmButtonClass: 'btn btn-confirm mt-2'
                                        }
                                    ).then(function() {
                                        location.reload();
                                    });
                                }
                            }
                    });
            }
      })
    }

            window.lock = function(member_id, user_id, member_name, status) {
            const isLock = (status == 1);

            // ตั้งค่า swal base
            const swalBase = Swal.mixin({
                confirmButtonText: 'ยืนยัน →',
                showCancelButton: true,
                cancelButtonText: 'ยกเลิก',
                progressSteps: ['1', '2'],
                reverseButtons: true
            });

            const confirmTitle = isLock ?
                'ต้องการล็อค Member หรือไม่' :
                'ต้องการปลดล็อค Member หรือไม่';

            // popup ยืนยัน
            swalBase.fire({
                title: confirmTitle
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '{{ route('managemember.memberlock') }}',
                        data: {
                            member_id: member_id,
                            user_id: user_id,
                            status: status
                        },
                        success: function(data) {
                            if (data !== false) {
                                const successTitle = isLock ? 'ล็อคการใช้งาน' : 'ปลดล็อคการใช้งาน';
                                const successMsg = isLock ? 'ถูกล็อคการใช้งานแล้ว' :
                                    'ถูกปลดล็อคการใช้งานแล้ว';

                                swalBase.fire({
                                    title: successTitle,
                                    html: `<h4>ชื่อผู้ใช้: ${member_name}</h4><h4>${successMsg}</h4>`,
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonText: 'ตกลง',
                                    customClass: {
                                        confirmButton: 'btn btn-confirm mt-2'
                                    }
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'ดำเนินการไม่สำเร็จ',
                                    text: 'โปรดลองอีกครั้งหรือแจ้งผู้ดูแลระบบ'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้'
                            });
                        }
                    });
                }
            });
        }


        window.delete_member = function(member_id, user_id, member_name) {
            const swalBase = Swal.mixin({
                confirmButtonText: 'ยืนยัน →',
                showCancelButton: true,
                cancelButtonText: 'ยกเลิก',
                progressSteps: ['1', '2'],
                reverseButtons: true
            });

            swalBase.fire({
                title: 'ต้องการลบสมาชิก หรือไม่',
                icon: 'warning'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '{{ route('managemember.memberdelete') }}',
                        data: {
                            member_id: member_id,
                            user_id: user_id
                        },
                        success: function(data) {
                            if (data !== false) {
                                Swal.fire({
                                    title: 'ลบสมาชิกใช้งาน',
                                    html: `<h4>ชื่อผู้ใช้: ${member_name}</h4><h4>สมาชิกถูกลบแล้ว</h4>`,
                                    icon: 'success',
                                    confirmButtonText: 'ตกลง',
                                    customClass: {
                                        confirmButton: 'btn btn-confirm mt-2'
                                    }
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'ดำเนินการไม่สำเร็จ',
                                    text: 'โปรดลองอีกครั้งหรือแจ้งผู้ดูแลระบบ'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้'
                            });
                        }
                    });
                }
            });
        }

    @if (session('success'))
        Swal.fire({
            position: 'top-end',
            type: 'success',
            title: 'Your work has been saved',
            showConfirmButton: false,
            timer: 1500
        })

    @endif
    </script>
@endsection
