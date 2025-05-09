@extends('base::email.layout')

@section('content')
    <p>Dear admin,</p>

    <p>This is an automated notification to inform you that a user has submitted feedback regarding system improvements through the feedback portal. Please do not reply to this email address.</p>

    <h3>Submission Details:</h3>

    <ul>
        <li>Submission Date: {{$submissionDate}}</li>
        <li>Employee ID: {{$employeeId}}</li>
        <li>Name: {{$feedbackName}}</li>
        <li>Email: {{$feedbackEmail}}</li>
    </ul>

    <h3>Feedback:</h3>

    <p>{{$feedbackComment}}</p>
@endsection
