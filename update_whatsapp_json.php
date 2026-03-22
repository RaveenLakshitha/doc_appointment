<?php
$file = __DIR__ . '/whatspp.json';
$json = json_decode(file_get_contents($file), true);

$json['routing_model'] = [
    "PREFERRED_LANGUAGE" => ["TYPE"],
    "TYPE" => ["DOCTOR_SELECT", "SPECIALIZATION"],
    "SPECIALIZATION" => ["AGE_GROUP"],
    "AGE_GROUP" => ["VISIT_TYPE"],
    "DOCTOR_SELECT" => ["VISIT_TYPE"],
    "VISIT_TYPE" => ["PATIENT_NEW", "PHONE_LOOKUP"],
    "PHONE_LOOKUP" => ["PATIENT_SELECT", "PREFERRED_TIME"],
    "PATIENT_SELECT" => ["PREFERRED_TIME"],
    "PATIENT_NEW" => ["PREFERRED_TIME"],
    "PREFERRED_TIME" => ["REASON"],
    "REASON" => ["APPOINTMENT_SUMMARY"],
    "APPOINTMENT_SUMMARY" => ["SUCCESS"],
    "SUCCESS" => []
];

$screens = [];
foreach ($json['screens'] as $screen) {
    if ($screen['id'] === 'PREFERRED_LANGUAGE') {
        // Data
        $screen['data']['lbl_heading'] = ["type" => "string"];
        $screen['data']['lbl_radio'] = ["type" => "string"];
        $screen['data']['lbl_btn'] = ["type" => "string"];
        // Layout
        $screen['layout']['children'][0]['text'] = '${data.lbl_heading}';
        $screen['layout']['children'][1]['children'][0]['label'] = '${data.lbl_radio}';
        $screen['layout']['children'][1]['children'][1]['label'] = '${data.lbl_btn}';
    }
    else if ($screen['id'] === 'TYPE') {
        $screen['data']['lbl_heading'] = ["type" => "string"];
        $screen['data']['lbl_body'] = ["type" => "string"];
        $screen['data']['lbl_radio'] = ["type" => "string"];
        $screen['data']['lbl_opt1'] = ["type" => "string"];
        $screen['data']['lbl_opt2'] = ["type" => "string"];
        $screen['data']['lbl_btn'] = ["type" => "string"];
        
        $screen['layout']['children'][0]['text'] = '${data.lbl_heading}';
        $screen['layout']['children'][1]['text'] = '${data.lbl_body}';
        $form = &$screen['layout']['children'][2]['children'];
        $form[0]['label'] = '${data.lbl_radio}';
        $form[0]['data-source'][0]['title'] = '${data.lbl_opt1}';
        $form[0]['data-source'][1]['title'] = '${data.lbl_opt2}';
        $form[1]['label'] = '${data.lbl_btn}';
    }
    else if ($screen['id'] === 'SPECIALIZATION') {
        $screen['data']['lbl_heading'] = ["type" => "string"];
        $screen['data']['lbl_body'] = ["type" => "string"];
        $screen['data']['lbl_dropdown'] = ["type" => "string"];
        $screen['data']['lbl_btn'] = ["type" => "string"];
        
        $screen['layout']['children'][0]['text'] = '${data.lbl_heading}';
        $screen['layout']['children'][1]['text'] = '${data.lbl_body}';
        $form = &$screen['layout']['children'][2]['children'];
        $form[0]['label'] = '${data.lbl_dropdown}';
        $form[1]['label'] = '${data.lbl_btn}';
        
        // Need to pass preferred_language along!
        $screen['data']['preferred_language'] = ["type" => "string"];
        // For SPECIALIZATION navigate footer:
        if (isset($form[1]['on-click-action']['payload'])) {
            $form[1]['on-click-action']['payload']['preferred_language'] = '${data.preferred_language}';
        }
    }
    else if ($screen['id'] === 'AGE_GROUP') {
        $screen['data']['lbl_heading'] = ["type" => "string"];
        $screen['data']['lbl_body'] = ["type" => "string"];
        $screen['data']['lbl_dropdown'] = ["type" => "string"];
        $screen['data']['lbl_btn'] = ["type" => "string"];
        
        $screen['layout']['children'][0]['text'] = '${data.lbl_heading}';
        $screen['layout']['children'][1]['text'] = '${data.lbl_body}';
        $form = &$screen['layout']['children'][2]['children'];
        $form[0]['label'] = '${data.lbl_dropdown}';
        $form[1]['label'] = '${data.lbl_btn}';
        
        $screen['data']['preferred_language'] = ["type" => "string"];
        if (isset($form[1]['on-click-action']['payload'])) {
            $form[1]['on-click-action']['payload']['preferred_language'] = '${data.preferred_language}';
        }
        $form[1]['on-click-action']['next']['name'] = 'VISIT_TYPE'; // Since preferred language is moved up!
    }
    else if ($screen['id'] === 'DOCTOR_SELECT') {
        $screen['data']['lbl_heading'] = ["type" => "string"];
        $screen['data']['lbl_body'] = ["type" => "string"];
        $screen['data']['lbl_dropdown'] = ["type" => "string"];
        $screen['data']['lbl_btn'] = ["type" => "string"];
        
        $form = &$screen['layout']['children'][0]['children'];
        $form[0]['text'] = '${data.lbl_heading}';
        $form[1]['text'] = '${data.lbl_body}';
        $form[2]['label'] = '${data.lbl_dropdown}';
        $form[3]['label'] = '${data.lbl_btn}';
        
        $screen['data']['preferred_language'] = ["type" => "string"];
        if (isset($form[3]['on-click-action']['payload'])) {
            $form[3]['on-click-action']['payload']['preferred_language'] = '${data.preferred_language}';
        }
    }
    else if ($screen['id'] === 'VISIT_TYPE') {
        $screen['data']['lbl_radio'] = ["type" => "string"];
        $screen['data']['lbl_opt1'] = ["type" => "string"];
        $screen['data']['lbl_opt2'] = ["type" => "string"];
        $screen['data']['lbl_btn'] = ["type" => "string"];
        
        $form = &$screen['layout']['children'][0]['children'];
        $form[0]['label'] = '${data.lbl_radio}';
        $form[0]['data-source'][0]['title'] = '${data.lbl_opt1}';
        $form[0]['data-source'][1]['title'] = '${data.lbl_opt2}';
        $form[1]['label'] = '${data.lbl_btn}';
    }
    else if ($screen['id'] === 'PHONE_LOOKUP') {
        $screen['data']['lbl_body'] = ["type" => "string"];
        $screen['data']['lbl_input'] = ["type" => "string"];
        $screen['data']['lbl_helper'] = ["type" => "string"];
        $screen['data']['lbl_btn'] = ["type" => "string"];
        
        $form = &$screen['layout']['children'][0]['children'];
        $form[0]['text'] = '${data.lbl_body}';
        $form[1]['label'] = '${data.lbl_input}';
        $form[1]['helper-text'] = '${data.lbl_helper}';
        $form[2]['label'] = '${data.lbl_btn}';
    }
    else if ($screen['id'] === 'PATIENT_SELECT') {
        $screen['data']['lbl_heading'] = ["type" => "string"];
        $screen['data']['lbl_body'] = ["type" => "string"];
        $screen['data']['lbl_radio'] = ["type" => "string"];
        $screen['data']['lbl_btn'] = ["type" => "string"];
        
        $screen['layout']['children'][0]['text'] = '${data.lbl_heading}';
        $screen['layout']['children'][1]['text'] = '${data.lbl_body}';
        $form = &$screen['layout']['children'][2]['children'];
        $form[0]['label'] = '${data.lbl_radio}';
        $form[1]['label'] = '${data.lbl_btn}';
    }
    else if ($screen['id'] === 'PATIENT_NEW') {
        $screen['data']['lbl_body'] = ["type" => "string"];
        $screen['data']['lbl_fname'] = ["type" => "string"];
        $screen['data']['lbl_lname'] = ["type" => "string"];
        $screen['data']['lbl_phone'] = ["type" => "string"];
        $screen['data']['lbl_email'] = ["type" => "string"];
        $screen['data']['lbl_age'] = ["type" => "string"];
        $screen['data']['lbl_age_h'] = ["type" => "string"];
        $screen['data']['lbl_gender'] = ["type" => "string"];
        $screen['data']['lbl_gen_m'] = ["type" => "string"];
        $screen['data']['lbl_gen_f'] = ["type" => "string"];
        $screen['data']['lbl_gen_o'] = ["type" => "string"];
        $screen['data']['lbl_address'] = ["type" => "string"];
        $screen['data']['lbl_psych'] = ["type" => "string"];
        $screen['data']['lbl_yes'] = ["type" => "string"];
        $screen['data']['lbl_no'] = ["type" => "string"];
        $screen['data']['lbl_time'] = ["type" => "string"];
        $screen['data']['lbl_time_h'] = ["type" => "string"];
        $screen['data']['lbl_ref'] = ["type" => "string"];
        $screen['data']['lbl_ref_h'] = ["type" => "string"];
        $screen['data']['lbl_btn'] = ["type" => "string"];
        
        $form = &$screen['layout']['children'][0]['children'];
        $form[0]['text'] = '${data.lbl_body}';
        $form[1]['label'] = '${data.lbl_fname}';
        $form[2]['label'] = '${data.lbl_lname}';
        $form[3]['label'] = '${data.lbl_phone}';
        $form[4]['label'] = '${data.lbl_email}';
        $form[5]['label'] = '${data.lbl_age}';
        $form[5]['helper-text'] = '${data.lbl_age_h}';
        $form[6]['label'] = '${data.lbl_gender}';
        $form[6]['data-source'][0]['title'] = '${data.lbl_gen_m}';
        $form[6]['data-source'][1]['title'] = '${data.lbl_gen_f}';
        $form[6]['data-source'][2]['title'] = '${data.lbl_gen_o}';
        $form[7]['label'] = '${data.lbl_address}';
        $form[8]['label'] = '${data.lbl_psych}';
        $form[8]['data-source'][0]['title'] = '${data.lbl_yes}';
        $form[8]['data-source'][1]['title'] = '${data.lbl_no}';
        $form[9]['label'] = '${data.lbl_time}';
        $form[9]['helper-text'] = '${data.lbl_time_h}';
        $form[10]['label'] = '${data.lbl_ref}';
        $form[10]['helper-text'] = '${data.lbl_ref_h}';
        $form[11]['label'] = '${data.lbl_btn}';
    }
    else if ($screen['id'] === 'PREFERRED_TIME') {
        $screen['data']['lbl_body'] = ["type" => "string"];
        $screen['data']['lbl_dropdown'] = ["type" => "string"];
        $screen['data']['lbl_opt1'] = ["type" => "string"];
        $screen['data']['lbl_opt2'] = ["type" => "string"];
        $screen['data']['lbl_opt3'] = ["type" => "string"];
        $screen['data']['lbl_btn'] = ["type" => "string"];
        
        $form = &$screen['layout']['children'][0]['children'];
        $form[0]['text'] = '${data.lbl_body}';
        $form[1]['label'] = '${data.lbl_dropdown}';
        $form[1]['data-source'][0]['title'] = '${data.lbl_opt1}';
        $form[1]['data-source'][1]['title'] = '${data.lbl_opt2}';
        $form[1]['data-source'][2]['title'] = '${data.lbl_opt3}';
        $form[2]['label'] = '${data.lbl_btn}';
    }
    else if ($screen['id'] === 'REASON') {
        $screen['data']['lbl_reason'] = ["type" => "string"];
        $screen['data']['lbl_reason_h'] = ["type" => "string"];
        $screen['data']['lbl_notes'] = ["type" => "string"];
        $screen['data']['lbl_btn'] = ["type" => "string"];
        
        $form = &$screen['layout']['children'][0]['children'];
        $form[0]['label'] = '${data.lbl_reason}';
        $form[0]['helper-text'] = '${data.lbl_reason_h}';
        $form[1]['label'] = '${data.lbl_notes}';
        $form[2]['label'] = '${data.lbl_btn}';
    }
    else if ($screen['id'] === 'APPOINTMENT_SUMMARY') {
        $screen['data']['lbl_heading'] = ["type" => "string"];
        $screen['data']['lbl_body'] = ["type" => "string"];
        $screen['data']['lbl_btn'] = ["type" => "string"];
        
        $screen['layout']['children'][0]['text'] = '${data.lbl_heading}';
        $screen['layout']['children'][1]['text'] = '${data.lbl_body}';
        $screen['layout']['children'][3]['label'] = '${data.lbl_btn}';
    }
    else if ($screen['id'] === 'SUCCESS') {
        $screen['data']['lbl_heading'] = ["type" => "string"];
        $screen['data']['lbl_btn'] = ["type" => "string"];
        
        $screen['layout']['children'][0]['text'] = '${data.lbl_heading}';
        $screen['layout']['children'][4]['label'] = '${data.lbl_btn}';
    }
    $screens[] = $screen;
}

$json['screens'] = $screens;

file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT));
echo "Done replacing!\n";
