<?php

namespace App\Exports;

use DB;
use App\MisLog;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class AnisCodesex implements
FromView


{


    private $cards;
   
    public function __construct($cards)
    {
      $this->cards = $cards;
   
    }
  
  public function view(): View
    {
      return view('AnisCodes.AnisCodesexcel', [
            'cards' =>$this->cards,
          

        ]);
    }


  public function title(): string
  {
    return 'Anis Sold cards';
  }
}
