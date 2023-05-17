@extends('adminViews::layouts.master')

@section('content')

<table border="0" cellspacing="0" cellpadding="0" width="600" align="center" style="color: #d4d2d2; font-size:13px; text-shadow: #423f3f 1px 1px"><tbody>

    <tr align="middle">
        <td style="background-color:#999999" height="25" colspan="7"><span style="FONT-SIZE: 14pt"><strong><font color="#000000">한국 전통색상</font></strong></span></td>
    </tr>

    <tr align="middle">
        <td style="background-color:#ffcccc" height="25"><strong>색이름</strong></td>
        <td style="background-color:#fff7a2" height="25"><strong>RGB</strong></td>
        <td style="background-color:#e0f4ff" height="25"><strong>CMYK</strong></td>
        <td style="background-color:#e5e5e5" height="25"><strong> </strong></td>
        <td style="background-color:#ffcccc" height="25"><strong>색이름</strong></td>
        <td style="background-color:#fff7a2" height="25"><strong>RGB</strong></td>
        <td style="background-color:#e0f4ff" height="25"><strong>CMYK</strong></td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25" colspan="3"><strong>무채색계 (無彩色界)</strong></td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:#ebfbff" height="25" colspan="3"><strong>청록색계 (靑綠色界)</strong></td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">흑백</td>
        <td style="background-color:#1d1e23" height="25"><font color="#ffffff">1D1E23</font></td>
        <td style="background-color:transparent" height="25">93,89,83,52</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">청색</td>
        <td style="background-color:#0b6db7" height="25">0B6DB7</td>
        <td style="background-color:transparent" height="25">89,56,0,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">백색</td>
        <td style="background-color:#ffffff" height="25">FFFFFF</td>
        <td style="background-color:transparent" height="25">0,0,0,0</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">벽색</td>
        <td style="background-color:#00b5e3" height="25">00B5E3</td>
        <td style="background-color:transparent" height="25">73,5,4,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">회색</td>
        <td style="background-color:#a4aaa7" height="25">A4AAA7</td>
        <td style="background-color:transparent" height="25">38,27,31,0</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">천청색</td>
        <td style="background-color:#5ac6d0" height="25">5AC6D0</td>
        <td style="background-color:transparent" height="25">59,0,20,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">구색</td>
        <td style="background-color:#959ea2" height="25">959EA2</td>
        <td style="background-color:transparent" height="25">45,32,32,0</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">담청색</td>
        <td style="background-color:#00a6a9" height="25">00A6A9</td>
        <td style="background-color:transparent" height="25">96,4,40,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">치색</td>
        <td style="background-color:#616264" height="25">616264</td>
        <td style="background-color:transparent" height="25">72,64,62,4</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">취람색</td>
        <td style="background-color:#5dc19b" height="25">5DC19B</td>
        <td style="background-color:transparent" height="25">62,0,51,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">연지회색</td>
        <td style="background-color:#6f606e" height="25">6F606E</td>
        <td style="background-color:transparent" height="25">55,58,40,20</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">양람색</td>
        <td style="background-color:#6c71b5" height="25">6C71B5</td>
        <td style="background-color:transparent" height="25">64,58,0,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">설백색</td>
        <td style="background-color:#dde7e7" height="25">DDE7E7</td>
        <td style="background-color:transparent" height="25">12,4,7,0</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">벽청색</td>
        <td style="background-color:#448ccb" height="25">448CCB</td>
        <td style="background-color:transparent" height="25">72,36,0,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">유배색</td>
        <td style="background-color:#e7e6d2" height="25">E7E6D2</td>
        <td style="background-color:transparent" height="25">9,5,18,0</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">청현색</td>
        <td style="background-color:#006494" height="25">006494</td>
        <td style="background-color:transparent" height="25">99,59,22,3</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">지배색</td>
        <td style="background-color:#e3ddcb" height="25">E3DDCB</td>
        <td style="background-color:transparent" height="25">6,6,17,4</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">감색</td>
        <td style="background-color:#026892" height="25">026892</td>
        <td style="background-color:transparent" height="25">93,57,26,2</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">소색</td>
        <td style="background-color:#d8c8b2" height="25">D8C8B2</td>
        <td style="background-color:transparent" height="25">10,15,26,5</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">남색</td>
        <td style="background-color:#6a5ba8" height="25">6A5BA8</td>
        <td style="background-color:transparent" height="25">68,73,0,0</td>
    </tr>

    <tr align="middle">
        <td height="25" colspan="3"> </td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">연람색</td>
        <td style="background-color:#7963ab" height="25">7963AB</td>
        <td style="background-color:transparent" height="25">60,69,0,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:#fff0f0" height="25" colspan="3"><strong>적색계 (赤色界)</strong></td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">벽람색</td>
        <td style="background-color:#6979bb" height="25">6979BB</td>
        <td style="background-color:transparent" height="25">64,52,0,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">적색</td>
        <td style="background-color:#b82647" height="25">B82647</td>
        <td style="background-color:transparent" height="25">21,98,68,8</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">숙람색</td>
        <td style="background-color:#45436c" height="25">45436C</td>
        <td style="background-color:transparent" height="25">86,84,40,9</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">홍색</td>
        <td style="background-color:#f15b5b" height="25">F15B5B</td>
        <td style="background-color:transparent" height="25">0,80,60,0</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">군청색</td>
        <td style="background-color:#4f599f" height="25">4F599F</td>
        <td style="background-color:transparent" height="25">80,73,6,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">적토색</td>
        <td style="background-color:#9f494c" height="25">9F494C</td>
        <td style="background-color:transparent" height="25">29,80,64,17</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">녹색</td>
        <td style="background-color:#417141" height="25">417141</td>
        <td style="background-color:transparent" height="25">82,44,95,9</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">휴색</td>
        <td style="background-color:#683235" height="25">683235</td>
        <td style="background-color:transparent" height="25">40,80,66,44</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">명록색</td>
        <td style="background-color:#16aa52" height="25">16AA52</td>
        <td style="background-color:transparent" height="25">81,5,94,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">갈색</td>
        <td style="background-color:#966147" height="25">966147</td>
        <td style="background-color:transparent" height="25">31,61,73,21</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">유록색</td>
        <td style="background-color:#6ab048" height="25">6AB048</td>
        <td style="background-color:transparent" height="25">64,8,97,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">호박색</td>
        <td style="background-color:#bd7f41" height="25">BD7F41</td>
        <td style="background-color:transparent" height="25">21,51,84,8</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">유청색</td>
        <td style="background-color:#569a49" height="25">569A49</td>
        <td style="background-color:transparent" height="25">72,20,96,1</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">추향색</td>
        <td style="background-color:#c38866" height="25">C38866</td>
        <td style="background-color:transparent" height="25">19,48,61,6</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">연두색</td>
        <td style="background-color:#c0d84d" height="25">C0D84D</td>
        <td style="background-color:transparent" height="25">29,0,87,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">육색</td>
        <td style="background-color:#d77964" height="25">D77964</td>
        <td style="background-color:transparent" height="25">11,62,59,2</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">춘유록색</td>
        <td style="background-color:#cbdd61" height="25">CBDD61</td>
        <td style="background-color:transparent" height="25">24,0,78,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">주색</td>
        <td style="background-color:#ca5e59" height="25">CA5E59</td>
        <td style="background-color:transparent" height="25">15,75,62,4</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">청록색</td>
        <td style="background-color:#009770" height="25">009770</td>
        <td style="background-color:transparent" height="25">97,15,74,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">주홍색</td>
        <td style="background-color:#c23352" height="25">C23352</td>
        <td style="background-color:transparent" height="25">18,94,60,5</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">진초록색</td>
        <td style="background-color:#0a8d5e" height="25">0A8D5E</td>
        <td style="background-color:transparent" height="25">87,26,82,1</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">담주색</td>
        <td style="background-color:#ea8474" height="25">EA8474</td>
        <td style="background-color:transparent" height="25">4,59,50,0</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">초록색</td>
        <td style="background-color:#1c9249" height="25">1C9249</td>
        <td style="background-color:transparent" height="25">85,20,98,2</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">진홍색</td>
        <td style="background-color:#bf2f7b" height="25">BF2F7B</td>
        <td style="background-color:transparent" height="25">20,94,17,4</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">흑록색</td>
        <td style="background-color:#2e674e" height="25">2E674E</td>
        <td style="background-color:transparent" height="25">89,52,83,9</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">선홍색</td>
        <td style="background-color:#ce5a9e" height="25">CE5A9E</td>
        <td style="background-color:transparent" height="25">16,79,2,0</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">비색</td>
        <td style="background-color:#72c6a5" height="25">72C6A5</td>
        <td style="background-color:transparent" height="25">55,0,45,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">연지색</td>
        <td style="background-color:#be577b" height="25">BE577B</td>
        <td style="background-color:transparent" height="25">19,77,28,7</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">옥색</td>
        <td style="background-color:#9ed6c0" height="25">9ED6C0</td>
        <td style="background-color:transparent" height="25">38,0,30,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">훈색</td>
        <td style="background-color:#d97793" height="25">D97793</td>
        <td style="background-color:transparent" height="25">9,64,20,2</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">삼청색</td>
        <td style="background-color:#5c6eb4" height="25">5C6EB4</td>
        <td style="background-color:transparent" height="25">71,59,0,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">진분홍색</td>
        <td style="background-color:#db4e9c" height="25">DB4E9C</td>
        <td style="background-color:transparent" height="25">9,84,0,0</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">뇌록색</td>
        <td style="background-color:#397664" height="25">397664</td>
        <td style="background-color:transparent" height="25">74,27,59,6</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">분홍색</td>
        <td style="background-color:#e2a6b4" height="25">E2A6B4</td>
        <td style="background-color:transparent" height="25">7,39,14,1</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">양록색</td>
        <td style="background-color:#31b675" height="25">31B675</td>
        <td style="background-color:transparent" height="25">74,0,74,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">연분홍색</td>
        <td style="background-color:#e0709b" height="25">E0709B</td>
        <td style="background-color:transparent" height="25">6,69,11,1</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">하염색</td>
        <td style="background-color:#245441" height="25">245441</td>
        <td style="background-color:transparent" height="25">83,43,75,39</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">장단색</td>
        <td style="background-color:#e16350" height="25">E16350</td>
        <td style="background-color:transparent" height="25">6,75,70,1</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">흑청색</td>
        <td style="background-color:#1583af" height="25">1583AF</td>
        <td style="background-color:transparent" height="25">84,39,17,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">석간주색</td>
        <td style="background-color:#8a4c44" height="25">8A4C44</td>
        <td style="background-color:transparent" height="25">30,71,65,30</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">청벽색</td>
        <td style="background-color:#18b4e9" height="25">18B4E9</td>
        <td style="background-color:transparent" height="25">69,8,0,0</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">흑홍색</td>
        <td style="background-color:#8e6f80" height="25">8E6F80</td>
        <td style="background-color:transparent" height="25">40,54,31,15</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td height="25" colspan="3"> </td>
    </tr>

    <tr align="middle">
        <td height="25" colspan="3"> </td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:#fff0f0" height="25" colspan="3"><strong>자색계 (紫色界)</strong></td>
    </tr>

    <tr align="middle">
        <td style="background-color:#fafad2" height="25" colspan="3"><strong>황색계 (黃色界)</strong></td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">자색</td>
        <td style="background-color:#6d1b43" height="25">6D1B43</td>
        <td style="background-color:transparent" height="25">41,95,45,40</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">황색</td>
        <td style="background-color:#f9d537" height="25">F9D537</td>
        <td style="background-color:transparent" height="25">3,13,89,0</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">자주색</td>
        <td style="background-color:#89236a" height="25">89236A</td>
        <td style="background-color:transparent" height="25">40,96,18,20</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">유황색</td>
        <td style="background-color:#ebbc6b" height="25">EBBC6B</td>
        <td style="background-color:transparent" height="25">6,25,67,1</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">보라색</td>
        <td style="background-color:#9c4998" height="25">9C4998</td>
        <td style="background-color:transparent" height="25">42,85,1,1</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">명황색</td>
        <td style="background-color:#fee134" height="25">FEE134</td>
        <td style="background-color:transparent" height="25">2,7,89,0</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">홍람색</td>
        <td style="background-color:#733e7f" height="25">733E7F</td>
        <td style="background-color:transparent" height="25">58,85,10,15</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">담황색</td>
        <td style="background-color:#f5f0c5" height="25">F5F0C5</td>
        <td style="background-color:transparent" height="25">4,2,27,0</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">포도색</td>
        <td style="background-color:#5d3462" height="25">5D3462</td>
        <td style="background-color:transparent" height="25">70,90,35,20</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">송화색</td>
        <td style="background-color:#f8e77f" height="25">F8E77F</td>
        <td style="background-color:transparent" height="25">4,4,62,0</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">청자색</td>
        <td style="background-color:#403f95" height="25">403F95</td>
        <td style="background-color:transparent" height="25">90,90,1,1</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">자황색</td>
        <td style="background-color:#f7b938" height="25">F7B938</td>
        <td style="background-color:transparent" height="25">2,29,89,0</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">벽자색</td>
        <td style="background-color:#84a7d3" height="25">84A7D3</td>
        <td style="background-color:transparent" height="25">47,25,1,1</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">행황색</td>
        <td style="background-color:#f1a55a" height="25">F1A55A</td>
        <td style="background-color:transparent" height="25">3,40,73,0</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">회보라색</td>
        <td style="background-color:#b3a7cd" height="25">B3A7CD</td>
        <td style="background-color:transparent" height="25">28,32,1,1</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">두록색</td>
        <td style="background-color:#e5b98f" height="25">E5B98F</td>
        <td style="background-color:transparent" height="25">8,27,45,1</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">담자색</td>
        <td style="background-color:#bea3c9" height="25">BEA3C9</td>
        <td style="background-color:transparent" height="25">23,36,1,1</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">적황색</td>
        <td style="background-color:#ed9149" height="25">ED9149</td>
        <td style="background-color:transparent" height="25">4,51,80,0</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">다자색</td>
        <td style="background-color:#47302e" height="25">47302E</td>
        <td style="background-color:transparent" height="25">75,86,85,35</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">토황색</td>
        <td style="background-color:#c8852c" height="25">C8852C</td>
        <td style="background-color:transparent" height="25">18,50,97,5</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25">적자색</td>
        <td style="background-color:#ba4160" height="25">BA4160</td>
        <td style="background-color:transparent" height="25">15,86,42,13</td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">지황색</td>
        <td style="background-color:#d6b038" height="25">D6B038</td>
        <td style="background-color:transparent" height="25">14,26,91,3</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">토색</td>
        <td style="background-color:#9a6b31" height="25">9A6B31</td>
        <td style="background-color:transparent" height="25">30,54,91,20</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">치자색</td>
        <td style="background-color:#f6cf7a" height="25">F6CF7A</td>
        <td style="background-color:transparent" height="25">3,18,61,0</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">홍황색</td>
        <td style="background-color:#dda28f" height="25">DDA28F</td>
        <td style="background-color:transparent" height="25">9,39,38,2</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">자황색</td>
        <td style="background-color:#bb9e8b" height="25">BB9E8B</td>
        <td style="background-color:transparent" height="25">22,33,40,7</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
    </tr>

    <tr align="middle">
        <td style="background-color:transparent" height="25">금색</td>
        <td style="background-color:#ffffff" height="25">
        </td><td style="background-color:transparent" height="25">별색</td>
        <td style="background-color:#e5e5e5" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
        <td style="background-color:transparent" height="25"> </td>
    </tr>

    <tr align="middle">
        <td> </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td> </td>
    </tr>

    </tbody></table>
@endsection
