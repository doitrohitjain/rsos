@extends('layouts.default')
@section('content')
<div>
<mat-form-field>
  <mat-label>Enter a date range</mat-label>
  <mat-date-range-input [formGroup]="range" [rangePicker]="picker">
    <input matStartDate formControlName="start" placeholder="Start date">
    <input matEndDate formControlName="end" placeholder="End date">
  </mat-date-range-input>
  <mat-hint>MM/DD/YYYY – MM/DD/YYYY</mat-hint>
  <mat-datepicker-toggle matIconSuffix [for]="picker"></mat-datepicker-toggle>
  <mat-date-range-picker #picker></mat-date-range-picker>

  <mat-error *ngIf="range.controls.start.hasError('matStartDateInvalid')">Invalid start date</mat-error>
  <mat-error *ngIf="range.controls.end.hasError('matEndDateInvalid')">Invalid end date</mat-error>
</mat-form-field>

<p></p>
</div>
<!-- <div class="input-group input-daterange">
    <input type="date" my-date-format="DD/MM/YYYY, hh:mm:ss"class="form-control" id="myLocalDate" >

</div> -->
@endsection


