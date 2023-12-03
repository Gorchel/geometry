<div class="footer">
    <ul style="list-style-type: none;
        border-bottom: 1px solid;
        padding: 0 0 20px;
        margin: 20px 0px 32px;">
        <li class="list__item">
            <span class="list__item-rhombus_red"
                  style="width: 0;
                height: 0;
                position: relative;
                display: inline-block;
                vertical-align: 6px;"></span>
            <span style="color: #ff0000;"><strong> - Агентам выплачиваем комиссионное вознаграждение.</strong></span>
        </li>
        <li class="list__item">
            <span class="list__item-rhombus_red" style="width: 0;
                height: 0;
                position: relative;
                display: inline-block;
                vertical-align: 6px;"></span>
            <span style="color: #ff0000;"><strong> - Покупаем ППА (переуступку прав аренды от города) и торговые помещения <br/> в Санкт-Петербурге и Москве.</strong></span>
        </li>
    </ul>
    <div>
        @foreach($renderFooterConfig as $config)
            <div style="padding-bottom: 5px; display: flex;">
                @if ($config['type'] == 'record')
                    <div style="width: 30%;"><strong>{{$config['title']}}</strong></div>
                    <div>{!! $config['value'] !!}</div>
                @elseif ($config['type'] == 'link')
                    @if (isset($config['logo']))
                        <div style="width: 30%; text-align: left;"><img style="width: 15%;" class="wa-img" src="{{$config['logo']}}" alt=""></div>
                        <div>
                            <a style="color: #591fd4;
                                text-decoration: underline;
                                display: inline-block;"
                               class="link" href="{{'//'.$config['value']}}" target="_blank">{{$config['title']}}</a>
                        </div>
                    @else
                        <div style="width: 30%;"><strong>{{$config['title']}}</strong></div>
                        <div>
                            <a style="color: #591fd4;
                                text-decoration: underline;
                                display: inline-block;"
                               class="link" href="{{ '//'.$config['value']}}" target="_blank">{{$config['value']}}</a>
                        </div>
                    @endif
                @endif
            </div>
        @endforeach
    </div>
</div>
