<h1>Posts</h1>
<div>
    @foreach ($posts as $post)
        <div>
            <h4>{{ $post->title }}</h4>
            
        </div>
    @endforeach
</div>