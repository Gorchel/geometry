@foreach($renderConfig as $config)
    @if ($config['type'] == 'record')
        <div class="group">
            <span class="offer_text-red" style="display: inline-block; {{isset($config['firstStyles']) ? $config['firstStyles'] : ''}}">
                <strong>{{$config['title'].': '}}</strong>
            </span>
            <span style="display: inline-block; {{isset($config['secondStyles']) ? $config['secondStyles'] : ''}}">
                {!!isset($config['secondTag']) ? '<'.$config['secondTag'].'>' : ''!!}
                    {!! str_replace("&nbsp;",'',strip_tags($config['value'])) !!} {{isset($config['postfix']) ? $config['postfix'] : ''}}
                {!!isset($config['secondTag']) ? '</'.$config['secondTag'].'>' : ''!!}
            </span>
        </div>
    @elseif ($config['type'] == 'link')
        <div class="group">
            @if (isset($config['logo']))
                <span class="offer_text-blue" style="text-align: left;">
                    <img style="width: 5%; margin-top: 10px;" src="{{$config['logo']}}" alt="">
                </span>
                <a class="link" href="//{{$config['value']}}" target="_blank">{{$config['title']}}</a>
            @else
                <span class="offer_text-blue" style="{{isset($config['firstStyles']) ? $config['firstStyles'] : ''}}">
                    <strong>{{$config['title']}}</strong>
                </span>
                @if (!empty($config['value']))
                    <a class="link" href="{{$config['value']}}" target="_blank">{{$config['value']}}</a>
                @endif
            @endif
        </div>
    @elseif ($config['type'] == 'title')
        <div class="col-td__category offer_text-red" style="{{isset($config['styles']) ? $config['styles'] : ''}}">
            <strong>{{$config['title']}}</strong>
        </div>
    @elseif ($config['type'] == 'array')
        <div class="group" style="display: flex; flex-direction:row;">
            <span class="offer_text-red" style="padding-right: 5px; {{isset($config['firstStyles']) ? $config['firstStyles'] : ''}}">
                <strong>{{$config['title']}}</strong>
            </span>
            <span style="{{isset($config['secondStyles']) ? $config['secondStyles'] : ''}}">
                @foreach($config['child'] as $child)
                    @if(isset($child['value']['titles']) && !empty($child['value']['titles']))
                        <span>{{implode(', ', $child['value']['titles'])}}: </span>
                    @endif

                    @if(isset($child['value']['items']) && !empty($child['value']['items']))
                        <span>{{implode(', ', $child['value']['items'])}}</span><br/>
                    @endif
                @endforeach
            </span>
        </div>
    @else
        <td style="border: 1px solid #000;
                        padding: 4px;
                        width: 50%;" colspan="2">{{ str_replace("&nbsp;",'',strip_tags($config['title'])) }}
        </td>
    @endif
@endforeach
