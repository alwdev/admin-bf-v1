@props(['bankName' => '', 'size' => 25])

@php
    $bankMap = [
        'ธนาคารกรุงเทพ'                          => 'bbl.png',
        'BBL'                                     => 'bbl.png',
        'ธนาคารกสิกรไทย'                         => 'kbank.png',
        'KBANK'                                   => 'kbank.png',
        'กสิกรไทย'                               => 'kbank.png',
        'ธนาคารกรุงไทย'                          => 'ktb.png',
        'KTB'                                     => 'ktb.png',
        'กรุงไทย'                                => 'ktb.png',
        'ธนาคารทหารไทยธนชาต'                     => 'ttb.png',
        'TTB'                                     => 'ttb.png',
        'ธนาคารไทยพาณิชย์'                       => 'scb.png',
        'SCB'                                     => 'scb.png',
        'ไทยพาณิชย์'                             => 'scb.png',
        'ธนาคารกรุงศรีอยุธยา'                    => 'bay.png',
        'BAY'                                     => 'bay.png',
        'กรุงศรี'                                => 'bay.png',
        'ธนาคารเกียรตินาคินภัทร'                 => 'kk.png',
        'KKP'                                     => 'kk.png',
        'ธนาคารซีไอเอ็มบีไทย'                   => 'cimb.png',
        'CIMB'                                    => 'cimb.png',
        'ธนาคารทิสโก้'                           => 'tisco.png',
        'TISCO'                                   => 'tisco.png',
        'ธนาคารยูโอบี'                           => 'uob.png',
        'UOB'                                     => 'uob.png',
        'ธนาคารไทยเครดิต'                        => 'tcrb.png',
        'TCRB'                                    => 'tcrb.png',
        'ธนาคารออมสิน'                           => 'gsb.png',
        'GSB'                                     => 'gsb.png',
        'ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร'  => 'baac.png',
        'BAAC'                                    => 'baac.png',
        'ธนาคารการค้า'                           => 'bcel.jpg',
        'BCEL'                                    => 'bcel.jpg',
        'ธนาคารลาวพัฒนา'                         => 'trust.jpg',
        'ธนาคารJDB'                              => 'jdb.jpg',
        'JDB'                                     => 'jdb.jpg',
        'TrueMoney Wallet'                        => 'truemoney.png',
        'truemoney'                               => 'truemoney.png',
        'askmepay'                                => 'askmepay.png',
    ];

    $normalized = trim($bankName ?? '');
    $imgFile = $bankMap[$normalized] ?? null;

    // fallback: ค้นหาแบบ case-insensitive
    if (!$imgFile && $normalized !== '') {
        foreach ($bankMap as $key => $file) {
            if (mb_strtolower($key) === mb_strtolower($normalized)) {
                $imgFile = $file;
                break;
            }
        }
    }
@endphp

@if($imgFile)
    <img src="{{ asset('images/bank/' . $imgFile) }}" width="{{ $size }}" height="{{ $size }}"
         class="bank-logo rounded-circle" style="object-fit:cover;"
         title="{{ $bankName }}" alt="{{ $bankName }}">
@elseif(trim($bankName ?? '') !== '')
    <span class="badge text-bg-secondary" style="font-size:0.65rem;" title="{{ $bankName }}">
        {{ mb_substr($normalized, 0, 4) }}
    </span>
@endif
