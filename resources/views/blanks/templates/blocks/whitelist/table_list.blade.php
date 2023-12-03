@foreach($renderConfig as $config)
        @if ($config['type'] == 'record')
            <p style="margin: 0px;">
                @if (!empty($config['title']))
                    <span style="{{isset($config['firstStyles']) ? $config['firstStyles'] : ''}}">
                        {!! isset($config['tag']) ? '<'.$config['tag'].'>'  : '' !!}
                        {{$config['title'].": "}}
                        {!! isset($config['tag']) ? '</'.$config['tag'].'>'  : '' !!}
                    </span>
                @endif
                <span style="{{isset($config['secondStyles']) ? $config['secondStyles'] : ''}}">
                    {!! isset($config['second_tag']) ? '<'.$config['second_tag'].'>'  : '' !!}
                    @if (isset($config['enable_tags']))
                        {!! $config['value'] !!}
                    @else
                        {{strip_tags($config['value'])}}
                    @endif
                    @if (isset($config['postfix']))
                        <span style="{{isset($config['postfixStyles']) ? $config['postfixStyles'] : ''}}">
                            {{$config['postfix']}}
                        </span>
                    @endif
                    {!! isset($config['second_tag']) ? '</'.$config['second_tag'].'>'  : '' !!}
                </span>
            </p>
        @elseif ($config['type'] == 'link')
            <p style="margin: 0px;">
                <span>{{$config['title'].': '}}</span>
                @if (!empty($config['value']))
                    <a style="color: #591fd4;
                            text-decoration: underline;
                            display: inline-block;"
                       class="link" href="{{$config['value']}}" target="_blank">{{$config['value']}}</a>
                @endif
            </p>
        @elseif ($config['type'] == 'nbsp')
            <p style="margin: 10px;"></p>
        @elseif ($config['type'] == 'title')
            <p style="margin: 0px; {{isset($config['firstStyles']) ? $config['firstStyles'] : ''}}">{!! isset($config['tag']) ? '<'.$config['tag'].'>'  : ''!!} {{ str_replace("&nbsp;",'',strip_tags($config['title'])) }} {!! isset($config['tag']) ? '</'.$config['tag'].'>'  : '' !!}</p>
        @elseif ($config['type'] == 'array')
            <div style="display: flex;">
                <div class="content__label" style="{{isset($config['firstStyles']) ? $config['firstStyles'] : ''}}">{!! isset($config['tag']) ? '<'.$config['tag'].'>'  : '' !!} {{$config['title']}}:&nbsp;{!! isset($config['tag']) ? '</'.$config['tag'].'>'  : ''!!} </div>
                <div class="content__value">
                    @if (!empty($config['child']))
                        @foreach($config['child'] as $child)
                            @if (!empty($child['value']))
                                @if(isset($child['value']['titles']) && !empty($child['value']['titles']))
                                    <span>{{implode(', ', $child['value']['titles'])}}: </span>
                                @endif

                                @if(isset($child['value']['items']) && !empty($child['value']['items']))
                                    <span>{{implode(', ', $child['value']['items'])}}</span><br/>
                                @endif
                            @endif
                        @endforeach
                    @else
                        <span>{!! isset($config['default']) ? $config['default'] : '' !!}</span>
                    @endif
                </div>
            </div>
        @else
            <div style="display: flex;">
                <div class="content__label" style="width: 12%;">{!! isset($config['tag']) ? '<'.$config['tag'].'>'  : ''!!} {{$config['title']}}: {!! isset($config['tag']) ? '</'.$config['tag'].'>'  : '' !!} </div>
                <div class="content__value" style="width: 88%;">{{ str_replace("&nbsp;",'',strip_tags($config['value'])) }}</div>
            </div>
        @endif
@endforeach
