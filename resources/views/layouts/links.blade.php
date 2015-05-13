@if (count($links))
    <ul class="fa-ul">
        @foreach ($links as  $k => $link)
            <li>
                <i class="fa fa-li fa-angle-right"></i>
                <a href="{!! $urls[$k] !!}">{!! $link['title'] !!}</a>
            </li>
        @endforeach
    </ul>
@endif
