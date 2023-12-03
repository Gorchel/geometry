@extends('layouts')

@section("content")
        <form action="/weebhook_estate_get" id="submitForm">
            <div class="row" style="margin-bottom: 40px;"></div>
            <input type="hidden" name="token" value="{{$token}}">
            <input type="hidden" name="id" value="{{$id}}">
            <div class="row">
                <div class="col-lg-10 offset-lg-1 form-group">
                @include('traits.updated_tables', [
                    'updatedTablesRecords' => $updatedTablesRecords
                ])
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10 offset-lg-1 form-group">
                    <select name="find_by_all" id="find_by_all" class="form-control">
                        <option value="0" {{$findByAll == 0 ? 'selected="selected"' : ''}}>Искать по всем</option>
                        <option value="1" {{$findByAll == 1 ? 'selected="selected"' : ''}}>Искать по одному</option>
                    </select>
                </div>
                @include('objects.notification', ['text' => 'Искать по всем: Совпадение по всем фильтрам. Искать по одному: Совпадение хотя бы по одному фильтру.'])
            </div>
            <div class="row">
                <div class="col-lg-10 offset-lg-1 form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input"  id="ignore-sending-entities-checkboxes" name="ignore_sending_entities" value="1">
                        <label class="custom-control-label" for="ignore-sending-entities-checkboxes">Игнорировать предыдущую рассылку</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10 offset-lg-1 form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input"  id="select-all-checkboxes" value="1">
                        <label class="custom-control-label" for="select-all-checkboxes">Все</label>
                    </div>
                    <select name="object_type" id="object_type" class="form-control">
                        <option value="4" {{$objectType == \App\Properties::RENT_TYPE ? 'selected="selected"' : ''}}>Сниму</option>
{{--                        <option value="1" {{$objectType == 1 ? 'selected="selected"' : ''}}>Сдам</option>--}}
{{--                        <option value="2" {{$objectType == 2 ? 'selected="selected"' : ''}}>Продам</option>--}}
                        <option value="3" {{$objectType == \App\Properties::BUY_TYPE ? 'selected="selected"' : ''}}>Куплю</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10 offset-lg-1 form-group">
                    <select name="city_type" id="city_type" class="form-control">
                        <option value="2" {{$cityTypeId == 2 ? 'selected="selected"' : ''}}>Санкт-Петербург</option>
                        <option value="1" {{$cityTypeId == 1 ? 'selected="selected"' : ''}}>Москва</option>
                    </select>
                </div>
                @include('objects.notification', ['text' => 'Выбор города.'])
            </div>
            <div class="row">
                <div class="col-lg-10 offset-lg-1 form-group">
                    <label for="stage_categories">Воронка</label>
                    <select name="stage_categories" id="stage_categories" class="form-control">
                        <option value=""></option>
                        @foreach($stages as $stage)
                            <option value="{{$stage['id']}}" {{$stage['id'] == \App\Properties::DEFAULT_FILTER_STAGE ? 'selected="selected"' : ''}}>{{$stage['name']}}</option>
                        @endforeach
                    </select>
                </div>
                @include('objects.notification', ['text' => 'Воронка создания сделки.'])
            </div>
            <div class="row">
                <div class="col-lg-10 offset-lg-1 form-group">
                    <label for="responsible_users">Ответственный</label>
                    <select name="responsible_users" id="responsible_users" class="form-control">
                        @foreach($users as $user)
                            <option value="{{$user['id']}}" {{$user['id'] == \App\Properties::DEFAULT_FILTER_USER ? 'selected="selected"' : ''}}>{{$user['name']}}</option>
                        @endforeach
                    </select>
                </div>
                @include('objects.notification', ['text' => 'Закрепите ответственнного за сделкой.'])
            </div>

            <div class="row">
                <div class="col-lg-10 offset-lg-1 form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="type_of_property_check" name="type_of_property_check" value="1">
                        <label class="custom-control-label" for="type_of_property_check">По типу недвижимости</label>
                    </div>
                    <select name="type_of_property[]" id="type_of_property" class="form-control" multiple="multiple">
                        @foreach ($typeOfProperties as $key => $value)
                            <option value="{{$value}}" {{in_array($value, $typeOfPropertyObj) ? 'selected="selected"' : ''}}>{{$value}}</option>
                        @endforeach
                    </select>
                </div>
                @include('objects.notification', ['text' => 'Совпадение хотя бы по одному значению.'])
            </div>
            @if ($objectType != 2)
                <div class="row">
                    <div class="col-lg-10 offset-lg-1 form-group">
                        <div class="custom-control custom-checkbox">
                            @if ($objectType == \App\Properties::BUY_TYPE)
                                <input type="checkbox" class="custom-control-input" id="type_of_activity_check" name="type_of_activity_check" value="1">
                            @else
                                <input type="checkbox" class="custom-control-input" id="type_of_activity_check" name="type_of_activity_check" checked="checked" value="1">
                            @endif

                            <label class="custom-control-label" for="type_of_activity_check">По Виду деятельности</label>
                        </div>
                        <select name="type_of_activity[]" id="type_of_activity" class="form-control" multiple="multiple">
                            @foreach ($objectTypes as $key => $value)
                                <option value="{{$value}}" {{in_array($value, $profileCompanies) ? 'selected="selected"' : ''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </div>
                    @include('objects.notification', ['text' => 'Совпадение хотя бы по одному значению.'])
                </div>
                <div class="row">
                    <div class="col-lg-10 offset-lg-1 form-group">
                        <div class="custom-control custom-checkbox">
                            @if ($objectType == \App\Properties::BUY_TYPE)
                                <input type="checkbox" class="custom-control-input" id="except_type_of_activity_check" name="except_type_of_activity_check" value="1">
                            @else
                                <input type="checkbox" class="custom-control-input" id="except_type_of_activity_check" name="except_type_of_activity_check" checked="checked" value="1">
                            @endif
                            <label class="custom-control-label" for="except_type_of_activity_check">Ограничения по Виду деятельности</label>
                        </div>
                        <select name="except_type_of_activity[]" id="except_type_of_activity" class="form-control" multiple="multiple">
                            @foreach ($objectTypes as $key => $value)
                                <option value="{{$value}}" {{in_array($value, $exceptProfileCompanies) ? 'selected="selected"' : ''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </div>
                    @include('objects.notification', ['text' => 'Совпадение хотя бы по одному значению.'])
                </div>
                <div class="row">
                    <div class="col-lg-10 offset-lg-1 form-group">
                        <div class="custom-control custom-checkbox">
                            @if ($objectType == \App\Properties::BUY_TYPE)
                                <input type="checkbox" class="custom-control-input" id="except_companies_check" name="except_companies_check" value="1">
                            @else
                                <input type="checkbox" class="custom-control-input" id="except_companies_check" name="except_companies_check" checked="checked" value="1">
                            @endif

                            <label class="custom-control-label" for="except_companies_check">Не предлагать компаниям</label>
                        </div>
                        <select name="except_companies[]" id="except_companies" class="form-control" multiple="multiple">
                            @foreach ($exceptCompaniesList as $key => $company)
                                <option value="{{$company['value']}}" {{in_array($company['value'], $exceptCompanies) ? 'selected="selected"' : ''}}>{{$company['value']}}</option>
                            @endforeach
                        </select>
                    </div>
                    @include('objects.notification', ['text' => 'Исключаем компании из выборки. Совпадение хотя бы по одному значению.'])
                </div>
                <div class="row">
                    <div class="col-lg-10 offset-lg-1 form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="streetHouseCheck" name="street_house_check" value="1">
                            <label class="custom-control-label" for="streetHouseCheck">Улица, Дом</label>
                        </div>
                        <input type="text" class="form-control input-sm" name="street_house" value="{{$addressHouse}}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-10 offset-lg-1 form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="streetCheck" name="street_check" value="1">
                            <label class="custom-control-label" for="streetCheck">Улица</label>
                        </div>
                        <input type="text" class="form-control input-sm" name="street" value="{{$address}}">
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-lg-10 offset-lg-1 form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="metroCheck" name="metro_check" value="1">
                        <label class="custom-control-label" for="metroCheck">Метро</label>
                    </div>
                    <select name="metro[]" id="metro" class="form-control" multiple="multiple">
                        @foreach ($metroSelect as $key => $value)
                            <option value="{{$value}}" {{strpos($metro,mb_strtolower($value)) == true ? 'selected="selected"' : ''}}>{{$value}}</option>
                        @endforeach
                    </select>
                </div>
                @include('objects.notification', ['text' => 'Совпадение хотя бы по одному значению.'])
            </div>
            <div class="row">
                <div class="col-lg-10 offset-lg-1 form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="districtCheck" name="district_check" value="1">
                        <label class="custom-control-label" for="districtCheck">Район</label>
                    </div>
                    <input type="text" class="form-control input-sm" name="district" value="{{isset($districtArray[0]) ? $districtArray[0] : ''}}">
                </div>
                @include('objects.notification', ['text' => 'Выбор по точному совпадению.'])
            </div>
            @if ($objectType == 1)
                <div class="row">
                    <div class="col-lg-10 offset-lg-1 form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="address_program_check" name="address_program_check" value="1">
                            <label class="custom-control-label" for="address_program_check">Есть ли адрессная программа?</label>
                        </div>
                        <select name="address_program" id="address_program" class="form-control">
                            <option value="Да">Да</option>
                            <option value="Нет">Нет</option>
                        </select>
                    </div>
                    @include('objects.notification', ['text' => 'Выбор по точному совпадению.'])
                </div>
            @endif
            @if ($objectType == 4)
                <div class="row">
                    <div class="col-lg-10 offset-lg-1 form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="address_program_send_check" name="address_program_send_check" value="1">
                            <label class="custom-control-label" for="address_program_send_check">Рассылка клиентов только по адресным программам?</label>
                        </div>
                        <select name="address_program_send" id="address_program_send" class="form-control">
                            <option value="Да">Да</option>
                            <option value="Нет">Нет</option>
                        </select>
                    </div>
                    @include('objects.notification', ['text' => 'Выбор по точному совпадению.'])
                </div>
            @endif
            <div class="row">
                <div class="col-lg-10 offset-lg-1 form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="filter_companies_check" name="filter_companies_check" checked="checked" value="1">

                        <label class="custom-control-label" for="filter_companies_check">Фильтр по компаниям</label>
                    </div>
                    <select name="filter_companies[]" id="filter_companies" class="form-control" multiple="multiple">
                        @foreach (\App\Company::CUSTOM_NETWORK_VALUES as $key => $value)
                            <option value="{{$value}}" {{$value == \App\Company::CUSTOM_NETWORK_DEFAULT ? 'selected="selected"' : ''}}>{{$value}}</option>
                        @endforeach
                    </select>
                </div>
                @include('objects.notification', ['text' => 'Исключаем компании из выборки. Совпадение хотя бы по одному значению.'])
            </div>
            @if (!empty($objectSlider['footage']))
                <div class="row">
                    <div class="col-lg-10 offset-lg-1 form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="custom-control custom-checkbox">
                                    @if ($objectType == \App\Properties::BUY_TYPE)
                                        <input type="checkbox" class="custom-control-input" id="customCheckOne" name="footage_check" value="1">
                                    @else
                                        <input type="checkbox" class="custom-control-input" id="customCheckOne" name="footage_check" value="1" checked="checked">
                                    @endif
                                    <label class="custom-control-label" for="customCheckOne">По площади (кв/м)</label>
                                </div>
                                <input id="footage" type="text" name="footage" class="btm-color" value="" data-slider-min="-100" data-slider-max="100" data-slider-step="5" data-slider-value="[-20,20]" style="width: 80%;"/>&nbsp;<b> %</b>
                            </div>
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-4">
                                        <input type="text" class="form-control change_value" data-key="footage" name="footage_start_input" value="0">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" class="form-control" name="footage_value_input" data-value="{{intval($objectSlider['footage'])}}" value="{{number_format(intval($objectSlider['footage']),0,' ',' ')}}" readonly="readonly">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" class="form-control change_value" data-key="footage" name="footage_finish_input" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('objects.notification', ['text' => 'Если хотя бы одна граница попадает в диапазон'])
                </div>
            @else
                <div class="row hidden">
                    <input id="footage" type="hidden" class="btm-color" value="" style="width: 80%;"/>&nbsp;<b></b>
                </div>
            @endif
            @if (!empty($objectSlider['budget_volume']))
                <div class="row">
                    <div class="col-lg-10 offset-lg-1 form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="customCheckTwo" name="budget_volume_check" value="1"  checked="checked">
                                    <label class="custom-control-label" for="customCheckTwo">По бюджету, руб.мес</label>
                                </div>
                                <input id="budget_volume" name="budget_volume" type="text" class="btm-color" value="" data-slider-min="-100" data-slider-max="100" data-slider-step="5" data-slider-value="[-100,20]" style="width: 80%;"/>&nbsp;<b> %</b>
                            </div>
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-4">
                                        <input type="text" class="form-control change_value" data-key="budget_volume" name="budget_volume_start_input" value="0">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" class="form-control" name="budget_volume_value_input" data-value="{{intval($objectSlider['budget_volume'])}}" value="{{number_format(intval($objectSlider['budget_volume']),0,' ',' ')}}" readonly="readonly">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" class="form-control change_value" data-key="budget_volume" name="budget_volume_finish_input" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('objects.notification', ['text' => 'Если хотя бы одна граница попадает в диапазон'])
                </div>
            @else
                <div class="row hidden">
                    <input id="budget_volume" type="hidden" class="btm-color" value="" style="width: 80%;"/>&nbsp;<b></b>
                </div>
            @endif
            @if (in_array($objectType, [1, 4]) && !empty($objectSlider['budget_volume']))
                <div class="row">
                    <div class="col-lg-10 offset-lg-1 form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="customCheckThree" name="budget_footage_check" value="1">
                                    <label class="custom-control-label" for="customCheckThree">По бюжету руб, кв.м.</label>
                                </div>
                                <input id="budget_footage" name="budget_footage" type="text" class="btm-color" value="" data-slider-min="-100" data-slider-max="100" data-slider-step="5" data-slider-value="[-20,20]" style="width: 80%;"/>&nbsp;<b> %</b>
                            </div>
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-4">
                                        <input type="text" class="form-control change_value" data-key="budget_footage" name="budget_footage_start_input" value="0">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" class="form-control" name="budget_footage_value_input" data-value="{{intval($objectSlider['budget_footage'])}}" value="{{number_format(intval($objectSlider['budget_footage']),0,' ',' ')}}" readonly="readonly">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" class="form-control change_value" data-key="budget_footage" name="budget_footage_finish_input" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('objects.notification', ['text' => 'Если хотя бы одна граница попадает в диапазон'])
                </div>
            @else
                <div class="row hidden">
                    <input id="budget_footage" type="hidden" class="btm-color" value="" data-slider-min="-100" data-slider-max="100" data-slider-step="5" data-slider-value="[-20,20]" style="width: 80%;"/>&nbsp;<b> %</b>
                </div>
            @endif
            @if (in_array($objectType, [3]))
                <div class="row">
                    <div class="col-lg-10 offset-lg-1 form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="payback_period_check" name="payback_period_check" value="1">
                                    <label class="custom-control-label" for="payback_period_check">Окупаемость мес. (S2)</label>
                                </div>
                                <input id="payback_period" name="payback_period" type="text" class="btm-color" value="" data-slider-min="0" data-slider-max="300" data-slider-step="5" data-slider-value="[120,140]" style="width: 80%;"/>&nbsp;<b>&nbsp;мес.</b>
                            </div>
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-4">
                                        <input type="text" class="form-control change_value" data-key="payback_period" name="payback_period_start_input" value="0">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" class="form-control" name="payback_period_value_input" data-value="130" value="130" readonly="readonly">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" class="form-control change_value" data-key="payback_period" name="payback_period_finish_input" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('objects.notification', ['text' => 'Если хотя бы одна граница попадает в диапазон'])
                </div>
            @else
                <div class="row hidden">
                    <input id="payback_period" type="hidden" class="btm-color" value="" data-slider-min="-100" data-slider-max="100" data-slider-step="5" data-slider-value="[-20,20]" style="width: 80%;"/>&nbsp;<b> %</b>
                </div>
            @endif
            @if (in_array($objectType, [3]))
                <div class="row">
                    <div class="col-lg-10 offset-lg-1 form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="rent_check" name="rent_check" value="1">
                                    <label class="custom-control-label" for="rent_check">Предполагаемая Аренда в месяц., в рублях.</label>
                                </div>
                                <input id="rent" name="rent" type="text" class="btm-color" value="" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="[-20,20]" style="width: 80%;"/>
                            </div>
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-4">
                                        <input type="text" class="form-control change_value" data-key="rent" name="rent_start_input" value="0">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" class="form-control" name="rent_value_input" data-value="{{intval($objectSlider['rent'])}}" value="{{number_format(intval($objectSlider['rent']),0,' ',' ')}}" readonly="readonly">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" class="form-control change_value" data-key="rent" name="rent_finish_input" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('objects.notification', ['text' => 'Если хотя бы одна граница попадает в диапазон'])
                </div>
            @else
                <div class="row hidden">
                    <input id="rent" type="hidden" class="btm-color" value="" data-slider-min="-100" data-slider-max="100" data-slider-step="5" data-slider-value="[-20,20]" style="width: 80%;"/>&nbsp;<b> %</b>
                </div>
            @endif
            @if (in_array($objectType, [3]))
                <div class="row">
                    <div class="col-lg-10 offset-lg-1 form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="income_check" name="income_check" value="1">
                                    <label class="custom-control-label" for="income_check">Предполагаемая доходность</label>
                                </div>
                                <input id="income" name="income" type="text" class="btm-color" value="" data-slider-min="0" data-slider-max="50" data-slider-step="1" data-slider-value="[8,12]" style="width: 80%;"/>&nbsp;<b> %</b>
                            </div>
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-4">
                                        <input type="text" class="form-control change_value" data-key="income" name="income_start_input" value="0">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" class="form-control" name="income_value_input" data-value="{{intval($objectSlider['income'])}}" value="{{number_format(intval($objectSlider['income']),0,' ',' ')}}" readonly="readonly">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" class="form-control change_value" data-key="income" name="income_finish_input" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('objects.notification', ['text' => 'Если хотя бы одна граница попадает в диапазон'])
                </div>
            @else
                <div class="row hidden">
                    <input id="income" type="hidden" class="btm-color" value="" data-slider-min="-100" data-slider-max="100" data-slider-step="5" data-slider-value="[-20,20]" style="width: 80%;"/>&nbsp;<b></b>
                </div>
            @endif

            @if (in_array($objectType, [1,2]))
                <div class="row">
                    <div class="col-lg-10 offset-lg-1 form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="client_type_check" name="client_type_check" value="1">
                            <label class="custom-control-label" for="client_type_check">Клиент</label>
                        </div>
                        <select name="client_type" id="client_type" class="form-control">
                            <option value="сетевой">сетевой</option>
                            <option value="не сетевой">не сетевой</option>
                        </select>
                    </div>
                    @include('objects.notification', ['text' => 'Выбор по точному совпадению.'])
                </div>
            @endif
            <div class="row">
                <div class="col-lg-10 offset-lg-1 form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="near_metro_check" name="near_metro_check" value="1">
                        <label class="custom-control-label" for="near_metro_check">Рядом с метро?</label>
                    </div>
                    <select name="near_metro" id="near_metro" class="form-control">
                        <option value="Да">Да</option>
                    </select>
                </div>
                @include('objects.notification', ['text' => 'Выбор по точному совпадению.'])
            </div>
            <div class="row">
                <div class="col-lg-10 offset-lg-1 form-group text-center">
                    <input type="submit" class="btn btn-success" id="submit" value="Создать сделку">
                </div>
            </div>
        </form>
        <div class="row hidden" id="loader">
            <div class="col-lg-10 offset-lg-1 form-group text-center">
                <h1 сlass="text-center">Загрузка</h1>
            </div>
        </div>
    @parent
@overwrite

@section('js')
    @parent

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/bootstrap-slider.min.js" integrity="sha512-f0VlzJbcEB6KiW8ZVtL+5HWPDyW1+nJEjguZ5IVnSQkvZbwBt2RfCBY0CBO1PsMAqxxrG4Di6TfsCPP3ZRwKpA==" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip()

            changeObjType();

            var footageSlider = new Slider('#footage', {}).on('change', function (ev) {
                updateSliderInput('footage', ev.newValue, 'round10');
            });

            var budgetVolumeSlider = new Slider('#budget_volume', {}).on('change', function (ev) {
                updateSliderInput('budget_volume', ev.newValue, 'round10000');
            });
            var budgetFootageSlider = new Slider('#budget_footage', {}).on('change', function (ev) {
                updateSliderInput('budget_footage', ev.newValue, 'round100');
            });
            var incomeSlider = new Slider('#income', {}).on('change', function (ev) {
                updateSliderInputWithoutPercent('income', ev.newValue);
            });
            var rentFootageSlider = new Slider('#rent', {}).on('change', function (ev) {
                updateSliderInput('rent', ev.newValue);
            });

            var paybackPeriodSlider = new Slider('#payback_period', {}).on('change', function (ev) {
                updateSliderInputWithoutPercent('payback_period', ev.newValue);
            });

            $('#type_of_activity').select2({
                closeOnSelect: false
            });

            $('#except_type_of_activity').select2({
                closeOnSelect: false
            });

            $('#type_of_property').select2({
                closeOnSelect: false
            });

            $('#metro').select2({
                closeOnSelect: false
            });

            $('#filter_companies').select2({
                closeOnSelect: false
            });

            $('#except_companies').select2({
                closeOnSelect: false
            });

            updateSliderInput('footage',footageSlider.getValue(), 'round10');
            updateSliderInput('budget_volume',budgetVolumeSlider.getValue(), 'round10000');
            updateSliderInput('budget_footage',budgetFootageSlider.getValue());
            updateSliderInput('rent',rentFootageSlider.getValue());
            updateSliderInputWithoutPercent('income',incomeSlider.getValue());
            updateSliderInputWithoutPercent('payback_period',paybackPeriodSlider.getValue());

            $('body').on('change','.change_value', function() {
               var key = $(this).data('key'),
                   startVal = parseInt($('input[name="' + key + '_start_input"]').val().split(' ').join('')),
                   realVal = parseInt($('input[name="' + key + '_value_input"]').data('value')),
                   finishVal = parseInt($('input[name="' + key + '_finish_input"]').val().split(' ').join(''));

               if (key === 'footage') {
                   footageSlider.setValue([updateSlider(realVal, startVal), updateSlider(realVal, finishVal)]);
               } else if (key === 'budget_volume') {
                   budgetVolumeSlider.setValue([updateSlider(realVal, startVal), updateSlider(realVal, finishVal)]);
               } else if (key === 'budget_footage') {
                   budgetFootageSlider.setValue([updateSlider(realVal, startVal), updateSlider(realVal, finishVal)]);
               } else if (key === 'rent') {
                   rentFootageSlider.setValue([updateSlider(realVal, startVal), updateSlider(realVal, finishVal)]);
               } else if (key === 'income') {
                   incomeSlider.setValue([updateSlider(realVal, startVal), updateSlider(realVal, finishVal)]);
               } else {
                   paybackPeriodSlider.setValue([updateSliderWithoutPercent(startVal), updateSliderWithoutPercent(finishVal)]);
               }

                $(this).val(numberWithSpaces($(this).val()));
            });

            $("input[type=text]").keydown(function(event){
                if(event.keyCode == 13){
                    event.preventDefault();
                    return false;
                }
            });

            $('body').on('change','select#city_type', function() {
                changeLocation();
            });

            $('body').on('change','select#object_type', function() {
                changeLocation();
            });

            $('body').on('change', '#select-all-checkboxes', function() {
                var value = $(this).prop('checked');

                $.each($('.custom-control-input'), function() {
                    $(this).prop('checked', value);
                });
            })

            $('#submitForm').on('submit', function(e) {
                e.preventDefault();

                $(this).hide();
                $('#loader').show();

                location.href = location.origin + '/weebhook_estate_get?' + $(this).serialize();
            })
        });

        function changeLocation()
        {
            location.href = location.href + '&object_type=' + $("select#object_type option:selected").val() + '&city_type=' + $("select#city_type option:selected").val() + '&find_by_all=' + $("select#find_by_all option:selected").val();
        }

        function updateSlider(realVal, value)
        {
            return ((value - realVal) / realVal) * 100;
        }

        function updateSliderWithoutPercent(realVal)
        {
            return realVal;
        }

        function updateSliderInput(key, valueArr, funcName = false)
        {
            var value = parseInt($('input[name="' + key + '_value_input"]').data('value')),
                start_val = getPercent(value, valueArr[0]),
                finish_val = getPercent(value, valueArr[1]),
                // start_input = Math.floor($('input[name="' + key + '_start_input"]')),
                // finish_input = Math.ceil($('input[name="' + key + '_finish_input"]'));
                start_input = $('input[name="' + key + '_start_input"]'),
                finish_input = $('input[name="' + key + '_finish_input"]');

            if (funcName) {
                start_val =  this[funcName](start_val);

                if (key === 'footage' && finish_val % 10 > 0) {
                    finish_val = this[funcName](finish_val) + 10;
                }

                if (key === 'budget_volume' && finish_val % 10000 > 0) {
                    finish_val = this[funcName](finish_val) + 10000;
                }

                if (key === 'budget_footage' && finish_val % 100 > 0) {
                    finish_val = this[funcName](finish_val) + 100;
                }
            }

            start_input.val(numberWithSpaces(start_val));
            start_input.data('value', start_val);
            finish_input.val(numberWithSpaces(finish_val));
            finish_input.data('value', finish_val);
        }

        function round10(val) {
            return Math.round(val/10) * 10
        }

        function round1000(val) {
            return Math.round(val/1000) * 1000
        }

        function round10000(val) {
            return Math.round(val/10000) * 10000
        }

        function round100(val) {
            return Math.round(val/100) * 100
        }

        function updateSliderInputWithoutPercent(key, valueArr)
        {
            var value = parseInt($('input[name="' + key + '_value_input"]').data('value')),
                start_val = valueArr[0],
                finish_val = valueArr[1],
                start_input = $('input[name="' + key + '_start_input"]'),
                finish_input = $('input[name="' + key + '_finish_input"]');

            start_input.val(numberWithSpaces(start_val));
            start_input.data('value', start_val);
            finish_input.val(numberWithSpaces(finish_val));
            finish_input.data('value', finish_val);
        }

        function getPercent(value, percent) {
            return value + parseInt((value / 100) * parseInt(percent));
        }

        function numberWithSpaces(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
        }

        function changeObjType() {
            var obj_type = $("select#object_type option:selected").val();

            $.each($('.change_obg_type'), function() {
                var _this = $(this),
                    data_type = _this.data('type');

                if (data_type == obj_type) {
                    if (_this.hasClass('hidden')) {
                        _this.removeClass('hidden');

                        _this.find('input[type=checkbox]').prop( "checked", true );
                    }
                } else {
                    if (!_this.hasClass('hidden')) {
                        _this.addClass('hidden');

                        _this.find('input[type=checkbox]').prop( "checked", false );
                    }
                }
            });
        }
    </script>
@overwrite

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/css/bootstrap-slider.min.css" integrity="sha512-3q8fi8M0VS+X/3n64Ndpp6Bit7oXSiyCnzmlx6IDBLGlY5euFySyJ46RUlqIVs0DPCGOypqP8IRk/EyPvU28mQ==" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .btm-color .slider-selection {
            background: #00e5ff;
        }

        .hidden {
            display: none;
        }
    </style>
@show
