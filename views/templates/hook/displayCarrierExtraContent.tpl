<div id="custom_input_date_hour_pickup" style="display: none;">
    <div class="col-sm-11 form-group row padding-0" style="align-items: center !important;display: flex;">
        <input
            type="hidden"
            value="{$carrier_id}"
            id="custom_input_date_hour_pickup_id"
        >
        <label for="{$custom_input1_name}" class="col-md-5 padding-0 margin-0">{$custom_input1_label} : </label>
        <div class="col-md-4 padding-0">
            <input
                    class="form-control datepicker_module_flydevclickandcollect input_flydevclickandcollect"
                    type="text"
                    value="{$custom_input1_value}"
                    id="{$custom_input1_name}"
                    name="{$custom_input1_name}"
                    placeholder="{$custom_input1_label}"
                    readonly
            >
        </div>
        <div class="col-md-3">
            <select class="form-control input_flydevclickandcollect" id="{$custom_input2_name}" name="{$custom_input2_name}">
                <option>Heure</option>
                {for $time=8 to 17}
                    <option value="{$time}:00" {if $custom_input2_value eq "{$time}:00"} selected {/if}>{$time}:00</option>
                    <option value="{$time}:15" {if $custom_input2_value eq "{$time}:15"} selected {/if}>{$time}:15</option>
                    <option value="{$time}:30" {if $custom_input2_value eq "{$time}:30"} selected {/if}>{$time}:30</option>
                    <option value="{$time}:45" {if $custom_input2_value eq "{$time}:45"} selected {/if}>{$time}:45</option>
                {/for}
            </select>
        </div>
    </div>
</div>