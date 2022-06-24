@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>Products Orders</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">Products Orders</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title" style="margin-bottom: 15px">@lang('site.products') <small>{{count($orders)}}</small></h3>

                 <!--   <form action="{{ route('dashboard.productsorders.index') }}" method="get">

                        <div class="row">

                        

                            <div class="col-md-4">
                                @if (auth()->user()->hasPermission('create_Cards'))
                                    <a href="{{ route('dashboard.products.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @endif
                            </div>

                        </div>
                    </form> end of form -->

               
                   
                </div><!-- end of box header -->

                <div class="box-body">

                    @if (isset($orders) > 0)

                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th>Address Id</th>
                               
                                <th>Arrival Date</th>
                                <th>Create Date</th>
                                <th>Discount</th>
                                <th>Order Number</th>
                                <th>Promo Code Id</th>
                                <th>Total</th>
                                 <th>User Id</th>
                               <th>Order Details</th> 
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($orders as $index=>$category)
                                <tr>
                                 
                                    
                                    <td>{{ $category->address_id }}</td>
                                    <td>{{ $category->arrival_date }}</td>
                                    <td>{{ $category->create_date }}</td>
                                    <td>{{ $category->discount }}</td>
                                   <td>{{ $category->order_number }}</td>
                                    <td>{{ $category->promocodeid }}</td>
                                   <td>{{ $category->total }}</td>
                                    <td>{{ $category->userid }}</td>


                                                                   <td>
                                    
                                     
                                            <a href="{{ route('dashboard.productsorderdetails', $category->id) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> Order Details</a>
                                         
                                           
                                        
                                  
                                       @if (auth()->user()->hasPermission('deleste_Cards'))
                                            <form action="{{ route('dashboard.products.destroy', $category->id) }}" method="post" style="display: inline-block">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                <input  type="hidden" name="card_id"value="{{$category->id}}">
                                                <button type="submit" class="btn btn-danger delete btn-sm"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                            </form><!-- end of form -->
                                 
                                        @endif

                                        
                                    </td>
                                </tr>

                            @endforeach
                            </tbody>

                        </table><!-- end of table -->

                        {{-- $Cards->appends(request()->query())->links() --}}

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
