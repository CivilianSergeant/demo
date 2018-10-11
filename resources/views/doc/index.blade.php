@extends('doc.home')

@section('body')
    @include('doc.page-style')
    <table  cellspacing="0" cellpadding="0" class="table version" style="width: 50%; background: unset;">
        <tr>
            <td width="217" valign="top"><strong>Document    Title: </strong></td>
            <td width="340" valign="top">PLAAS TECH SMART IPTV / OTT API</td>
        </tr>
        <tr>
            <td width="217" valign="top"><strong>Version: </strong></td>
            <td width="340" valign="top">1.01</td>
        </tr>
        <tr>
            <td width="217" valign="top"><strong>Date: </strong></td>
            <td width="340" valign="top"><strong>2016-06-01</strong></td>
        </tr>
        <tr>
            <td width="217" height="21" valign="top"><strong>Status: </strong></td>
            <td width="340" valign="top"><strong>Release </strong></td>
        </tr>
    </table>

    <div class="title">
        <strong>General  Notes </strong>
    </div>
    <div class="content">
        <p>
            PLAAS TECH offers this information as a service to  its customers, to support application and engineering efforts that use the  products designed by PLAAS TECH. The information provided is based upon  requirements specifically provided to PLAAS TECH by the customers. PLAAS TECH  has not undertaken any independent search for additional relevant information,  including any information that may be in the customer&rsquo;s possession.  Furthermore, system validation of this product designed by PLAAS TECH within a  larger electronic system remains the responsibility of the customer or the  customer&rsquo;s system integrator. All specifications supplied herein are subject to  change. </p>
    </div>



    <br/>
    <table class="table api">

        <tbody>
        <tr>
            <td><a  href="{{url('doc/api')}}"><strong>API Document</strong></a></td>
        </tr>
        <tr>
            <td><a  href="{{url('simulator')}}"><strong>API Simulator</strong></a></td>
        </tr>
        <tr>
            <td><a  href="{{url('http://test.viewersent.com.bd/live')}}"><strong>API Emulator</strong></a></td>
        </tr>
        <tr>
            <td><a  href="{{url('doc/process-flow')}}"><strong>Process Flow</strong></a></td>
        </tr>

        </tbody>
    </table>

    <div class="title"><strong>Copyright </strong></div>
    <div class="content">
        <p>
            This document contains proprietary technical  information which is the property of Shanghai PLAAS TECH Wireless Solutions  Ltd, copying of this document and giving it to others and the using or  communication of the contents thereof, are forbidden without express authority.  Offenders are liable to the payment of damages. All rights reserved in the  event of grant of a patent or the registration of a utility model or design.  All specification supplied herein are subject to change without notice at any  time. </p>
    </div>
    <p>&nbsp;</p>
    <div class="title-font-10">
        <strong><em>Copyright &copy; PLAAS TECH INDUSTRIES Ltd. 2016 Warning .</em></strong>
    </div>
    <div class="content">
        <p>
            This  document includes some confidential information. It cannot be copied, modified,  or translated in another language without prior written authorization from  PLAAS. Any usage of this document without authorization from PLAAS will be  defined as infringing act.</p>
    </div>
@stop