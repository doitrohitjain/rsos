<form action="{{ route('rahul123') }}" method="post" enctype="multipart/form-data">

        @csrf

        <div class="row">

            <div class="col-md-12">

                <br/>

                <input type="file" name="image" class="image">

            </div>

            <div class="col-md-12">

                <br/>

                <button type="submit" class="btn btn-success">Upload Image</button>

            </div>

        </div>

    </form>