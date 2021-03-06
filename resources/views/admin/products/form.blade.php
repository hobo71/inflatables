<div class="col-md-12">
    <div class="block">
        <div class="block-title">
            <h2>Product</h2>
        </div>

        <div class="form-bordered">

            <div class="form-group">
                {!! label('category') !!}
                {!! select('categories', $nestedList, $parentId, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! label('name') !!}
                {!! text('name', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! label('description') !!}
                {!! textarea('description', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! label('accessories') !!}<br/>
                @if(isset($product))
                    You can manage accessories <a href="{{route('admin.products.show', $product->id)}}">here</a>.
                @else
                    You can add accessories once you you create a new product.
                @endif
            </div>

            <div class="form-group">
                {!! label('Wet') !!}
                {!! radio('season', 'wet', null) !!}
                {!! label('Dry') !!}
                {!! radio('season', 'dry', null) !!}
                {!! label('Both') !!}
                {!! radio('season', 'both', null) !!}
            </div>

            <div class="form-group">
                {!! label('images') !!}<br/>
                @if(isset($product))
                    You can manage images <a href="{{route('admin.products.show', $product->id)}}">here</a>.
                @else
                    You can add images once you you create a new product.
                @endif
            </div>

            <div class=" form-group">
                {!! label('enabled') !!}
                {!! checkbox('enabled', true) !!}
            </div>

            <div class=" form-group">
                {!! label('featured') !!}
                {!! checkbox('featured') !!}
            </div>
        </div>

    </div>

    <div class="form-group form-actions">
        {!!submit('Save Product', ['class' => 'btn btn-block btn-primary'])!!}
    </div>
</div>

