<table style="width: 100%;
        border-collapse: collapse;
        table-layout: fixed;">
    <tbody>
    @foreach($renderConfig as $config)
        <tr>
            @if ($config['type'] == 'record')
                <td style="border: 1px solid #000;
                        padding: 4px;
                        {{isset($config['firstStyles']) ? $config['firstStyles'] : ''}}"
                ><strong>{{$config['title'].': '}}</strong></td>
                <td style="border: 1px solid #000;
                        padding: 4px;"
                >{!! $config['value'] !!} {{isset($config['postfix']) ? $config['postfix'] : ''}}</td>
            @elseif ($config['type'] == 'link')
                <td style="border: 1px solid #000;
                        padding: 4px;;
                        {{isset($config['firstStyles']) ? $config['firstStyles'] : ''}}"
                ><strong>{{$config['title']}}</strong></td>
                <td style="border: 1px solid #000;
                        padding: 4px;">
                    @if (!empty($config['value']))
                        <a style="color: #591fd4;
                            text-decoration: underline;
                            display: inline-block;"
                           class="link" href="{{$config['value']}}" target="_blank">{{isset($config['name']) ? $config['name'] : $config['value']}}</a>
                    @endif
                </td>
            @elseif ($config['type'] == 'title')
                <td style="border: 1px solid #000;
                        width: 50%;
                        padding: 4px;
                        text-align: center;
                        {{isset($config['styles']) ? $config['styles'] : ''}}" colspan="2">
                    <strong>{{$config['title']}}</strong>
                </td>
            @elseif ($config['type'] == 'array')
                <td style="border: 1px solid #000;
                        padding: 4px;
                        {{isset($config['firstStyles']) ? $config['firstStyles'] : ''}}">
                    <strong>{{$config['title']}}</strong>
                </td>
                <td style="border: 1px solid #000;
                        padding: 4px;"
                >
                    @foreach($config['child'] as $child)
                        @if(isset($child['value']['titles']) && !empty($child['value']['titles']))
                            <span>{{implode(', ', $child['value']['titles'])}}: </span>
                        @endif

                        @if(isset($child['value']['items']) && !empty($child['value']['items']))
                            <span>{{implode(', ', $child['value']['items'])}}</span><br/>
                        @endif
                    @endforeach
                </td>
            @else
                <td style="border: 1px solid #000;
                        padding: 4px;
                        width: 50%; word-wrap: break-word;" colspan="2">{{ str_replace("&nbsp;",'',strip_tags($config['title'])) }}
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
