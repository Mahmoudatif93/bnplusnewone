@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>Anis Sold cards</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">Anis Sold cards</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title" style="margin-bottom: 15px">Anis Sold cards  <small>{{ $Cards->total() }}</small></h3>



           

               
                   
                </div><!-- end of box header -->

                <div class="box-body">

                    @if ($Cards->count() > 0)

                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th>#</th>
                              
                                <th>Client Name</th>
                              <th>Client Number</th>
                                <th>Order Id</th>
                                 <th>Card Name</th>
                                <th>@lang('site.card_code')</th>
                                <th>Payment Type</th>
                                 <th>Created At</th>
                                
                                
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($Cards as $index=>$category)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    
                                    <td>{{ $category->orders->client_name }}</td>
                                    <td>{{ $category->orders->client_number }}</td>
                                    <td>{{ $category->order_id }}</td>
                                    <?php $cardnames=\App\Cards::where(['id' => $category->orders->card_id])->first();?>
                                <td>{{ $cardnames->card_name }}</td>
                                    
                                     <td>{{ $category->card_code }}</td>
                                    <td>{{ $category->orders->paymenttype }}</td>
                                    <td>{{ $category->orders->created_at }}</td>
                                    
                                 

                                </tr>

                            @endforeach
                            </tbody>

                        </table><!-- end of table -->

                        {{ $Cards->appends(request()->query())->links() }}

                    @else

                        <h2>@lang('site.no_data_found')</h2>

                    @endif

                </div><!-- end of box body -->


            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->



    <script type="text/javascript">
    $(document).ready(function() {
        $('select[name="company_id"]').on('change', function() {
            var company_id = $(this).val();
            if(company_id) {
                $.ajax({
                    url: 'compcard/'+company_id,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {

                        
                        $('select[name="card_price"]').empty();
                        $.each(data, function(key, value) {
                            $('select[name="card_price"]').append('<option value="'+ value +'">'+ value +'</option>');
                        });


                    }
                });
            }else{
                $('select[name="card_price"]').empty();
            }
        });
    });
</script>



@endsection
