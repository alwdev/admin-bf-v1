@extends('layouts.guest')
@section('styles')
<style>
    .badge {

        padding: 10px;
        font-weight: 400;
    }
    .select2{

        width: 100% !important;

    }
</style>


@endsection
@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">{{__('main.report_error_transfert')}}</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                    <li class="breadcrumb-item active">{{__('main.report_error_transfert')}}</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12 card">

        <div class="card-body">
            @if (session('status'))
               <div class="alert alert-success" role="alert">
                {{ session('status') }}
                </div>
            @elseif ( session('error'))
                <div class="alert alert-danger" role="alert">
                 {{ session('error') }}
                </div>
            @endif
            <h4 class="card-title"></h4>
            <p class="card-subtitle mb-4">
            </p>

            <a type="button" class="btn btn-primary btn-gold waves-effect waves-light" href="javascript:void(0);" data-toggle="modal" data-target="#staticBackdrop">{{__('main.Add_Report')}}</a>
            <!-- Modal -->
            <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">{{__('main.report_error_transfert')}}</h1>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
                    </div>
                    <form id="form-add-transfer" action="{{ route('transfer.wrongdeposit_insert') }}" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="amount">{{__('managemember.amount')}}</label>
                                        <input type="number" class="form-control" id="amount" name="amount" autocomplete="off" required value="{{ old('amount') }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="member_id">{{__('managemember.user_name')}}</label>
                                        <input type="text" class="form-control" list="members" id="member_id" name="member_id" required>
                                        <datalist id="members">
                                            @foreach ($members as $item)
                                            <option value="{{ $item->username }}">
                                            @endforeach
                                        </datalist>
                                        @error('member_id')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="bank_from_name">{{__('managemember.BankAccount')}}</label>
                                                <select name="bank_from_name" id="bank_from_name" required class="form-control">
                                                    <option value="">{{__('managemember.Select_a_bank')}}</option>
                                                    <option value="ธนาคารกรุงเทพ" data-img="bbl.png" data-logo="{{ asset('images/bank/bbl.png') }}"> ธนาคารกรุงเทพ</option>
                                                    <option value="ธนาคารกสิกรไทย" data-img="kbank.png" data-logo="{{ asset('images/bank/kbank.png') }}"> ธนาคารกสิกรไทย</option>
                                                    <option value="ธนาคารกรุงไทย" data-img="ktb.png" data-logo="{{ asset('images/bank/ktb.png') }}"> ธนาคารกรุงไทย</option>
                                                    <option value="ธนาคารทหารไทยธนชาต" data-img="ttb.png" data-logo="{{ asset('images/bank/ttb.png') }}"> ธนาคารทหารไทยธนชาต</option>
                                                    <option value="ธนาคารไทยพาณิชย์" data-img="scb.png" data-logo="{{ asset('images/bank/scb.png') }}"> ธนาคารไทยพาณิชย์</option>
                                                    <option value="ธนาคารกรุงศรีอยุธยา" data-img="bay.png" data-logo="{{ asset('images/bank/bay.png') }}"> ธนาคารกรุงศรีอยุธยา</option>
                                                    <option value="ธนาคารเกียรตินาคินภัทร" data-img="kk.png" data-logo="{{ asset('images/bank/kk.png') }}"> ธนาคารเกียรตินาคินภัทร</option>
                                                    <option value="ธนาคารซีไอเอ็มบีไทย" data-img="cimb.png" data-logo="{{ asset('images/bank/cimb.png') }}"> ธนาคารซีไอเอ็มบีไทย</option>
                                                    <option value="ธนาคารทิสโก้" data-img="tisco.png" data-logo="{{ asset('images/bank/tisco.png') }}"> ธนาคารทิสโก้</option>
                                                    <option value="ธนาคารยูโอบี" data-img="uob.png" data-logo="{{ asset('images/bank/uob.png') }}"> ธนาคารยูโอบี</option>
                                                    <option value="ธนาคารไทยเครดิต" data-img="tcrb.png" data-logo="{{ asset('images/bank/tcrb.png') }}"> ธนาคารไทยเครดิต</option>
                                                    <option value="ธนาคารออมสิน" data-img="gsb.png" data-logo="{{ asset('images/bank/gsb.png') }}"> ธนาคารออมสิน</option>
                                                    <option value="ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร" data-img="baac.png" data-logo="{{ asset('images/bank/baac.png') }}"> ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร</option>
                                                    <option value="TrueMoney Wallet" data-img="truemoney.png" data-logo="{{ asset('images/bank/truemoney.png') }}"> TrueMoney Wallet</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="bank_from_account_name">{{__('managemember.Account_Name')}}</label>
                                                <input type="text" class="form-control" id="bank_from_account_name" name="bank_from_account_name" value="{{ old('bank_from_account_name') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="bank_from_number">{{__('managemember.Account_number')}}</label>
                                                <input type="text" class="form-control" id="bank_from_number" name="bank_from_number" value="{{ old('bank_from_number') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="bank_to">{{__('managemember.Recipient_Bank_Account')}}</label>
                                        <select name="bank_to" class="form-control" id="bank_to">
                                            <option></option>
                                            @foreach ($banks as $item)
                                            <option value="{{ $item->id }}">{{ $item->bank_name.' ( '.$item->account_name.' - '.$item->account_name.' )' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="image">{{__('managemember.slip')}}</label>
                                        <input type="file" class="form-control" id="image" name="image" required>
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="note">{{__('managemember.note')}}</label>
                                        <textarea class="form-control" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                                    </div>
                                </div>
                            </div>

                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('main.close')}}</button>
                    <button type="submit" class="btn btn-primary" >{{__('main.save')}}</button>
                    </div>
                </form>
                </div>
                </div>
            </div>
            <!-- end row-->
            <table id="basic-datatable" class="table nowrap"
            data-filter-control="true"
                data-toggle="table"
                data-search="true"
                data-show-export="false"
                data-click-to-select="false"
                data-pagination="true"
                data-url="">
                <thead  class="table-light">
                    <tr>
                        <th data-field="username" data-sortable="true">{{__('dashboard.member')}}</th>
                        <th data-field="amount" data-sortable="true">{{__('managemember.amount')}}</th>
                        <th data-sortable="true">{{__('dashboard.Date_of_transaction')}}</th>
                        <th data-sortable="true">{{__('managemember.from')}}</th>
                        <th data-sortable="true">{{__('managemember.to')}}</th>
                        <th data-sortable="true">{{__('managemember.evidence')}}</th>
                        <th data-sortable="true"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transfer as $key => $item)
                        <tr>
                            <td>{{ $item->username }}</td>
                            <td>{{ (float) $item->amount }}</td>
                            <td>{{ date('d/m/Y H:i:s',$item->date) }}</td>
                            <td>
                                {{ $item->bank_from_name }} ( {{ $item->bank_from_account_name }} - {{ $item->bank_from_number }} )
                            </td>
                            <td>
                                {{ $item->bank_to_name }} ( {{ $item->bank_to_account_name }} -  {{ $item->bank_to_number }})
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm" onclick="showEvidence('{{ env('APP_URL_IMAGE_EVIDENCE').$item->image }}')">{{__('managemember.evidence')}}</button>
                            </td>

                            <td>
                              <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalEdit{{ $key }}">{{__('main.edit')}}</button>

                              <div class="modal fade" id="modalEdit{{ $key }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalEditLabel{{ $key }}" aria-hidden="true">
                                <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="modalEditLabel{{ $key }}">{{__('main.report_error_transfert')}}</h1>
                                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
                                    </div>
                                    <form id="form-add-transfer{{ $key }}" action="{{ route('transfer.wrongdeposit_update') }}" method="post" enctype="multipart/form-data">
                                    <div class="modal-body">
                                            @csrf
                                             <input type="hidden" name="id" value="{{ $item->id }}">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="amount">{{__('managemember.amount')}}</label>
                                                        <input type="number" class="form-control" id="amount{{ $key }}" name="amount" autocomplete="off" required value="{{ $item->amount }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="member_id">{{__('managemember.user_name')}}</label>
                                                        <input type="text" class="form-control" list="members{{ $key }}" id="member_id{{ $key }}" name="member_id" required value="{{ $item->username }}">
                                                        <datalist id="members{{ $key }}">
                                                            @foreach ($members as $item3)
                                                            <option value="{{ $item3->username }}">
                                                            @endforeach
                                                        </datalist>
                                                        @error('member_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="bank_from_name">{{__('managemember.BankAccount')}}</label>
                                                                <select name="bank_from_name" id="bank_from_name{{ $key }}" required class="form-control">
                                                                    <option value="">{{__('managemember.Select_a_bank')}}</option>
                                                                    <option  @if($item->bank_from_name == 'ธนาคารกรุงเทพ') selected @endif value="ธนาคารกรุงเทพ" data-img="bbl.png" data-logo="{{ asset('images/bank/bbl.png') }}"> ธนาคารกรุงเทพ</option>
                                                                    <option  @if($item->bank_from_name == 'ธนาคารกสิกรไทย') selected @endif value="ธนาคารกสิกรไทย" data-img="kbank.png" data-logo="{{ asset('images/bank/kbank.png') }}"> ธนาคารกสิกรไทย</option>
                                                                    <option  @if($item->bank_from_name == 'ธนาคารกรุงไทย') selected @endif value="ธนาคารกรุงไทย" data-img="ktb.png" data-logo="{{ asset('images/bank/ktb.png') }}"> ธนาคารกรุงไทย</option>
                                                                    <option  @if($item->bank_from_name == 'ธนาคารทหารไทยธนชาต') selected @endif value="ธนาคารทหารไทยธนชาต" data-img="ttb.png" data-logo="{{ asset('images/bank/ttb.png') }}"> ธนาคารทหารไทยธนชาต</option>
                                                                    <option  @if($item->bank_from_name == 'ธนาคารไทยพาณิชย์') selected @endif value="ธนาคารไทยพาณิชย์" data-img="scb.png" data-logo="{{ asset('images/bank/scb.png') }}"> ธนาคารไทยพาณิชย์</option>
                                                                    <option  @if($item->bank_from_name == 'ธนาคารกรุงศรีอยุธยา') selected @endif value="ธนาคารกรุงศรีอยุธยา" data-img="bay.png" data-logo="{{ asset('images/bank/bay.png') }}"> ธนาคารกรุงศรีอยุธยา</option>
                                                                    <option  @if($item->bank_from_name == 'ธนาคารเกียรตินาคินภัทร') selected @endif value="ธนาคารเกียรตินาคินภัทร" data-img="kk.png" data-logo="{{ asset('images/bank/kk.png') }}"> ธนาคารเกียรตินาคินภัทร</option>
                                                                    <option  @if($item->bank_from_name == 'ธนาคารซีไอเอ็มบีไทย') selected @endif value="ธนาคารซีไอเอ็มบีไทย" data-img="cimb.png" data-logo="{{ asset('images/bank/cimb.png') }}"> ธนาคารซีไอเอ็มบีไทย</option>
                                                                    <option  @if($item->bank_from_name == 'ธนาคารทิสโก้') selected @endif value="ธนาคารทิสโก้" data-img="tisco.png" data-logo="{{ asset('images/bank/tisco.png') }}"> ธนาคารทิสโก้</option>
                                                                    <option  @if($item->bank_from_name == 'ธนาคารยูโอบี') selected @endif value="ธนาคารยูโอบี" data-img="uob.png" data-logo="{{ asset('images/bank/uob.png') }}"> ธนาคารยูโอบี</option>
                                                                    <option  @if($item->bank_from_name == 'ธนาคารไทยเครดิต') selected @endif value="ธนาคารไทยเครดิต" data-img="tcrb.png" data-logo="{{ asset('images/bank/tcrb.png') }}"> ธนาคารไทยเครดิต</option>
                                                                    <option  @if($item->bank_from_name == 'ธนาคารออมสิน') selected @endif value="ธนาคารออมสิน" data-img="gsb.png" data-logo="{{ asset('images/bank/gsb.png') }}"> ธนาคารออมสิน</option>
                                                                    <option  @if($item->bank_from_name == 'ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร') selected @endif value="ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร" data-img="baac.png" data-logo="{{ asset('images/bank/baac.png') }}"> ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="bank_from_account_name">{{__('managemember.Account_Name')}}</label>
                                                                <input type="text" class="form-control" id="bank_from_account_name{{ $key }}" name="bank_from_account_name" value="{{ $item->bank_from_account_name }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="bank_from_number">{{__('managemember.Account_number')}}</label>
                                                                <input type="text" class="form-control" id="bank_from_number{{ $key }}" name="bank_from_number" value="{{ $item->bank_from_number }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="bank_to">{{__('managemember.Recipient_Bank_Account')}}</label>
                                                        <select name="bank_to" class="form-control" id="bank_to{{ $key }}">
                                                            <option></option>
                                                            @foreach ($banks as $bank)
                                                            <option value="{{ $bank->id }}" @if($item->bank_to_number == $bank->account_no) selected @endif>{{ $bank->bank_name.' ( '.$bank->account_name.' - '.$bank->account_name.' )' }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>


                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="image">{{__('managemember.slip')}}</label>
                                                        <input type="file" class="form-control" id="image{{ $key }}" name="image">
                                                        <img src="{{ asset($item->image) }}" style="max-width:400px;">
                                                    </div>
                                                </div>


                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="note">{{__('managemember.note')}}</label>
                                                        <textarea class="form-control" id="note{{ $key }}" name="note" rows="3">{{ $item->note }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('main.close')}}</button>
                                    <button type="submit" class="btn btn-primary" >{{__('main.save')}}</button>
                                    </div>
                                </form>
                                </div>
                                </div>
                            </div>

                              {{-- <button type="button" class="btn btn-danger btn-sm">Delete</button> --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

@endsection
@section('scripts')

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
        // $('#basic-datatable').DataTable({
        //     "order": [[ 2, "desc" ]]
        // });

    @if (session('status'))

        Swal.fire({
            position: 'top-end',
            type: 'success',
            title: 'Your work has been saved',
            showConfirmButton: false,
            timer: 1500
        })

    @endif
    @if (session('del_status'))

        Swal.fire({
            position: 'top-end',
            type: 'success',
            title: 'Your work has been saved',
            showConfirmButton: false,
            timer: 1500
        })

    @endif

        function showEvidence($img){
            Swal.fire({
                imageUrl: $img,
                imageHeight: 600,
                imageAlt: "A tall image"
            });
        }

        function approveDeposit(form){
            Swal.fire({
                    title: 'ต้องการอัพเดตสถานะหรือไม่?',
                    text: "***คำเตือนหากเป็นการ ถอนเงิน Admin ต้องทำรายการโอนเงินเองที่แอปธนาคาร",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่',
                    cancelButtonText: 'ไม่, ยกเลิก!',
                    confirmButtonClass: 'btn btn-success mt-2',
                    cancelButtonClass: 'btn btn-danger ml-2 mt-2',
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                       $(form).submit();
                    }
                });
        }
        @error('member_id')
            $('#staticBackdrop').modal('show');
        @enderror
    </script>
@endsection
