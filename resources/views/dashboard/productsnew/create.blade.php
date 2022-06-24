@extends('layouts.dashboard.app')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <h1>@lang('site.products')</h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
            <li><a href="{{ route('dashboard.products.index') }}"> @lang('site.products')</a></li>
            <li class="active">@lang('site.add')</li>
        </ol>
    </section>

    <section class="content">

        <div class="box box-primary">

            <div class="box-header">
                <h3 class="box-title">@lang('site.add')</h3>
            </div><!-- end of box header -->
            <div class="box-body">

                @include('partials._errors')

                <form action="{{ route('dashboard.products.store') }}" method="post" enctype="multipart/form-data">

                    {{ csrf_field() }}
                    {{ method_field('post') }}



                 
                            <div class="form-group col-6">
                                <label>@lang('site.name')</label>
                                <input type="text" name="name" class="form-control ">
                            </div>


<div class="form-group col-6"> <label>@lang('site.Companies')</label>
                        <select name="company_id" id="company_id" class="form-control">
                            <option value="">@lang('site.Companies')</option>
                            @foreach ($Companies as $row)
                            <option value="{{ $row->id }}" {{ old('company_id') == $row->id ? 'selected' : '' }}>{{ $row->companyname }}</option>
                    @endforeach
                    </select>
            </div>
            
            
             <div class="form-group col-6">
                                <label>code</label>
                                <input type="text" name="code" class="form-control ">
                            </div>
            
            
   <div class="form-group col-6">
                                <label>@lang('site.price')</label>
                                <input type="text" name="price" class="form-control ">
                            </div>


   <div class="form-group col-6">
                                <label>@lang('site.amount')</label>
                                <input type="number" name="amount" class="form-control ">
                            </div>
                    





      
        <div class="form-group">
            <button type="submit" id="submit" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')</button>
        </div>

        </form><!-- end of form -->

</div><!-- end of box body -->

</div><!-- end of box -->

</section><!-- end of content -->

</div><!-- end of content wrapper -->


<script>
    function add_row() {

        var frist_input = document.getElementById("bouquet_div").firstElementChild.outerHTML;



        $('#bouquet_div').append(frist_input);

    }

    function remove_row() {
        var last_input = document.getElementById("bouquet_div").lastChild;

        var len = $('#bouquet_div input').length;
        console.log('len :: ' + len);
        if (len >= 2) {
            document.getElementById("bouquet_div").removeChild(last_input);

        }

    }

    function changeautomanual() {

        if (document.getElementById("checkmanual").checked == true) {
            document.getElementById("automatic").style.display = "none";
            document.getElementById("manual").style.display = "block";

        } else {
            document.getElementById("manual").style.display = "none";
            document.getElementById("automatic").style.display = "block";

        }

    }

    function nationalcompanyfun() {

        if (document.getElementById("nationalcompany").checked == true) {

            document.getElementById("notnationaldiv").style.display = "none";
              document.getElementById("companies").style.display = "none";
              document.getElementById("submit").disabled = true;


        } else {
            document.getElementById("notnationaldiv").style.display = "block";
                document.getElementById("companies").style.display = "block";
              document.getElementById("submit").disabled = false;


        }

    }
</script>


<script type="text/javascript">
    $(document).ready(function() {
        $('select[name="kind"]').on('change', function() {
            var kind = $(this).val();
            if (kind) {
                $.ajax({
                    url: 'compcard/' + kind,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="company_id"]').empty();
                        $.each(data, function(key, value) {
                            $('select[name="company_id"]').append('<option value="' + value['id'] + '">' + value['name'] + '</option>');


                        });
                        if (kind == "national") {
                            document.getElementById("nationalcheck").style.display = "block";
                            
                            

                        } else {
                            document.getElementById("nationalcheck").style.display = "none";
                          


                        }

                    }
                });
            } else {
                $('select[name="company_id"]').empty();
            }
        });
    });
</script>


@endsection