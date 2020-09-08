<?php

//action.php

$received_data = json_decode(file_get_contents("php://input"));
$data = array();
if(isset($received_data->action) && $received_data->action == 'fetchall')
{
    $data = json_decode(file_get_contents("http://delivery.chalk247.com/team_list/NFL.JSON?api_key=74db8efa2a6db279393b433d97c2bc843f8e32b0"));
    echo json_encode($data);
}
if(isset($received_data->action) && $received_data->action == 'fetchallbyconferencedivision')
{
    $result = json_decode(file_get_contents("http://delivery.chalk247.com/team_list/NFL.JSON?api_key=74db8efa2a6db279393b433d97c2bc843f8e32b0"));

    foreach($result->results->data->team as $row)
    {
        if(
            ($received_data->conference != '') && ($received_data->division != '')
        ) {
            if (
                ($row->conference == $received_data->conference) && ($row->division == $received_data->division)
            ) {
                $data[] = $row;
            }
        } else if (
            ($received_data->conference != '' && $row->conference == $received_data->conference)
        ) {
            $data[] = $row;
        } else if (
            ($received_data->division != '' && $row->division == $received_data->division)
        ) {
            $data[] = $row;
        } else if (
            ($received_data->conference == '' && $received_data->division == '')
        ) {
            $data[] = $row;
        }
    }

    echo json_encode($data);
}
if(isset($received_data->action) && $received_data->action == 'fetchSingle')
{
    $result = json_decode(file_get_contents("http://delivery.chalk247.com/team_list/NFL.JSON?api_key=74db8efa2a6db279393b433d97c2bc843f8e32b0"));

    foreach($result->results->data->team as $row)
    {
        if($row->id == $received_data->id) {
            $data['id'] = $row->id;
            $data['name'] = $row->name;
            $data['nickname'] = $row->nickname;
            $data['display_name'] = $row->display_name;
            $data['conference'] = $row->conference;
            $data['division'] = $row->division;
        }
    }

    echo json_encode($data);
}

if(isset($received_data->request_for) && $received_data->request_for == 'conference')
{
    $result = json_decode(file_get_contents("http://delivery.chalk247.com/team_list/NFL.JSON?api_key=74db8efa2a6db279393b433d97c2bc843f8e32b0"));

    foreach($result->results->data->team as $row)
    {
        if(!in_array($row->conference, $data)) {
            $data[$row->conference] = $row->conference;
        }
    }

    echo json_encode($data);
}

if(isset($received_data->request_for) && $received_data->request_for == 'division')
{
    $result = json_decode(file_get_contents("http://delivery.chalk247.com/team_list/NFL.JSON?api_key=74db8efa2a6db279393b433d97c2bc843f8e32b0"));

    foreach($result->results->data->team as $row)
    {
        if(!in_array($row->division, $data)) {
            $data[] = $row->division;
        }
    }

    echo json_encode($data);
}
?>
