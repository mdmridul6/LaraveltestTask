<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    <title>Test Laravel</title>
</head>

<body>
    <div class="container mt-5">
        <div class="col-md-6 mt-2">
            <div class="d-flex justify-content-between">
                <input type="text" name="" id="" class="form-coltrol">
                <button class="btn btn-sm btn-info">Find</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mt-2">
                <select name="product" id="product" class="form-control">
                    <option value="0">Select Product</option>
                </select>
            </div>
            <div class="col-md-3 mt-2">
                <select name="product" id="product" class="form-control">
                    <option value="0">Select Customer</option>
                </select>
            </div>
            <div class="col-md-3 mt-2">
                <input type="date" name="date" id="date" class="form-control">
            </div>
        </div>

        <div class="mt-5">
            <table class="table table-bordered ">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Rate</th>
                        <th>Quantity</th>
                        <th>Total Amount</th>
                        <th>Discount (Amt)</th>
                        <th>Net Amoount</th>
                    </tr>
                </thead>
                <tbody id="result">

                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="row g-3 d-flex justify-content-between">
                            <div class="col-auto">
                                <label for="totalAmount" class="col-form-label">Total Amount</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" id="totalAmount" class="form-control" value="0">
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row g-3 d-flex justify-content-between">
                            <div class="col-auto">
                                <label for="totalDiscountAmount" class="col-form-label">Total Discount</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" id="totalDiscountAmount" class="form-control" value="0">
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row g-3 d-flex justify-content-between">
                            <div class="col-auto">
                                <label for="totalNetAmount" class="col-form-label">Total Net Amount</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" id="totalNetAmount" class="form-control" value="0">
                            </div>
                        </div>
                    </li>
                </ul>

            </div>
        </div>
    </div>


    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#product').select2({
                ajax: {
                    url: "{{ route('get.product') }}",
                    method: "GET",
                    data: function(params) {
                        var query = {
                            search: params.term,
                        }
                        return query;
                    },
                    datatype: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processResults: function(data) {

                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.name,
                                    id: item.id,
                                    rate: item.rate
                                }
                            })
                        };
                    },

                    cache: true,

                }
            });


            $('#product').on('select2:select', function(e) {
                var data = e.params.data;
                var html = null;
                html += '<tr>'

                html += '<td>' + data.text + '</td>';

                html += '<td>' + data.rate + '</td>';
                html += '<td><input type="text" id="quantity' + data.id +
                    '" name="quantity[]" onkeyup="countRate(' + data.rate + ',' + data.id +
                    ')" class="form-control" value="1"></td>';



                html += '<td id="totalAmount' + data.id + '">' + data.rate +
                    '</td><input type="hidden" name="totalAmountInput[]" id="totalAmountInput' + data.id +
                    '">';


                html += '<td><input type="text" name="discount[]" id="discount' + data.id +
                    '" onkeyup="countRateWithDiscount(' + data.id +
                    ')" class="form-control" value="0"></td>';


                html += '<td id="netAmount' + data.id + '">' + data.rate +
                    '</td><input type="hidden" name="netAmountInput[]" id="netAmountInput' + data.id + '">';

                html += '</tr>';
                $('#result').append(html);
                countRate(data.rate, data.id);
            })
        })


        function countRate(rate, id) {
            var quantity = $('#quantity' + id).val();
            $('#totalAmount' + id).html(quantity * rate);
            $('#totalAmountInput' + id).val(quantity * rate);
            countRateWithDiscount(id);
        }

        function countRateWithDiscount(id) {
            var totalAmount = $('#totalAmountInput' + id).val();
            var discountAmount = $('#discount' + id).val();
            $('#netAmount' + id).html(totalAmount - discountAmount);
            $('#netAmountInput' + id).val(totalAmount - discountAmount);
            summary()
        }

        function summary(params) {

            var totalAmount = 0;
            var totalDiscountAmount = 0;
            var totalNetamount = 0;
            totalAmount = $("input[name='totalAmountInput[]']")
                .map(function() {
                    return $(this).val();
                }).get();
            totalDiscountAmount = $("input[name='discount[]']")
                .map(function() {
                    return $(this).val();
                }).get();
            totalNetamount = $("input[name='netAmountInput[]']")
                .map(function() {
                    return $(this).val();
                }).get();




            let totalAmountSum = totalAmount.reduce((totalAmountSum, a) => parseInt(totalAmountSum) + parseInt(a), 0);
            let totalDiscountAmountSum = totalDiscountAmount.reduce((totalDiscountAmountSum, b) => parseInt(
                totalDiscountAmountSum) + parseInt(b), 0);
            let totalNetamountSum = totalNetamount.reduce((totalNetamountSum, c) => parseInt(totalNetamountSum) + parseInt(
                c), 0);



            $('#totalAmount').val(totalAmountSum);
            $('#totalDiscountAmount').val(totalDiscountAmountSum);
            $('#totalNetAmount').val(totalNetamountSum);
        }
    </script>
</body>

</html>
