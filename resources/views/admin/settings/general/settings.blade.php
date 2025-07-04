<div class="tab-pane mt-3 fade @if(!empty($social)) show active @endif" id="app-settings" role="tabpanel" aria-labelledby="settings">
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6">
            <form action="{{ getAdminPanelUrl() }}/settings/app-settings/store" method="post">
                {{ csrf_field() }}

                <input type="hidden" name="page" value="general">
                <input type="hidden" name="app_setting" value="{{ !empty($socialKey) ? $socialKey : 'app_setting' }}">

                <div class="form-group">
                    <label>{{ trans('admin/main.title') }}</label>
                    <input placeholder="Enter text that clearly describes this feature/setting e.g DisplayStateField" type="text" name="value[title]" value="{{ (!empty($social)) ? $social->title : old('value.title') }}" class="form-control  @error('value.title') is-invalid @enderror"/>
                    @error('value.title')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Value</label>
                    <input placeholder="Enter the feature/setting's value e.g Yes" type="text" name="value[value]" value="{{ (!empty($social)) ? $social->value : old('value.value') }}" class="form-control  @error('value.value') is-invalid @enderror"/>
                    @error('value.value')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success mt-3">{{ trans('admin/main.submit') }}</button>
            </form>
        </div>
    </div>

    <div class="table-responsive mt-5">
        <table class="table table-striped font-14">
            <tr>
                <th>{{ trans('public.title') }}</th>
                <th>Value</th>
                <th>{{ trans('public.controls') }}</th>
            </tr>

            @if(!empty($itemValue))
                @php
                    if (!is_array($itemValue)) {
                        $itemValue = json_decode($itemValue, true);
                    }
                @endphp

                @if(!empty($itemValue) and is_array($itemValue))
                    @foreach($itemValue as $key => $val)
                        <tr>
                            <td>{{ $val['title'] }}</td>
                            <td>{{ $val['value'] ?? "" }}</td>
                            <td>
                                <a href="{{ getAdminPanelUrl() }}/settings/app-settings/{{ $key }}/edit" class="btn-transparent text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                    <i class="fa fa-edit"></i>
                                </a>

                                @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/settings/app-settings/'. $key .'/delete','btnClass' => ''])
                            </td>
                        </tr>
                    @endforeach
                @endif
            @endif
        </table>
    </div>
</div>
