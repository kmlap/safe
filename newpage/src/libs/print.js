import printJS from 'print-js'

export const printHtmlCustomStyle = (id) => {
  const style = '@page { margin: 0; } @media print { #printTest{ padding: 30px 10px; } ' +
    'table {' +
      'width: 100% !important;' +
      'text-align: center;' +
      'font-family: verdana,arial,sans-serif;' +
      'font-size:11px;' +
      'color:#333333;' +
      'border-width: 1px;' +
      'border-color: #666666;' +
      'border-collapse: collapse;' +
    '}' +
    'table td, table th {' +
      'border-width: 1px;' +
      'width: 50px !important;' +
      'padding: 8px;' +
      'border-style: solid;' +
      'border-color: #666666;' +
      'word-wrap: break-word;' +
    '}' +
    'table th {' +
      'background-color: #dedede;' +
    '}' +
    'table td {' +
      'background-color: #ffffff;' +
    '}' +
  '}' // 直接写样式

  printJS({
    printable: id, // 要打印内容的id
    type: 'html',
    style: style,
    scanStyles: false
  })
}

export const printHtml = (id) => {
  printJS({
    printable: id, // 要打印内容的id
    type: 'html'
  })
}

export const printPage = (id) => {
  let style = '@media print { td,th{text-align: center;padding: 5px 0;border: 1px solid #000;} .ivu-form-item{overflow: hidden;padding: 5px 0;} .ivu-form-item .ivu-form-item-label{float:  left;width: auto !important;} .ivu-row{width: 100%;ooverflow: hidden;} '
  for(let i = 1; i <= 24; i++) {
    let width = i / 24 * 100
    style += '.ivu-col-span-' + i + '{width: '+ width +'%;float: left;}'
  }
  style += '.expense-table{border-collapse:collapse;margin:5px auto;text-align:center;width:100%}.expense-table td,.expense-table th{border:1px solid #e5e5e5;color:#666;height:30px;padding:0 5px}.expense-table thead th{background-color:#f8f8f9;width:100px}.expense-table tr:nth-child(1){background:#f8f8f9}.expense-table tr:nth-child(even){background:#fff}.expense-table .money span:last-child,.expense-table .money span:last-child{border-right:0}.expense-table .money{background:#f8f8f9;display:flex!important}.expense-table .money-box .money-content{background:#fff;display:block;height:100%;overflow:hidden}.expense-table .money-box .money-content td{padding:0;// float:left;// height:100%;height:68px}.expense-table .money-box{padding:0;width:225px}.expense-table .money span,.expense-table .money span{border-top:0;border-bottom:0;border-left:none;padding-left:0;padding-right:0;text-align:center;flex:1;border-right:1px solid #e5e5e5}.lineh-67{line-height:67px}.lineh-29{line-height:29px}.capital-num{display:inline-block;width:40px}.note{text-align:left;padding:28px}.note .note-form-iten{margin:0}.note .note-string{padding-left:10px}input:focus{outline:0}.table-input{width:100%;text-align:center}.table-input-box{padding-top:10px}.total-arabic-numerals{height: 29px;border:0}input::-webkit-outer-spin-button,input::-webkit-inner-spin-button{-webkit-appearance:none;appearance:none;margin:0}input{color: #000;background-color:transparent;-moz-appearance:textfield}.expense-table-icon-style{font-size:14px;cursor:pointer}.money-th{padding:0!important;width:225px}.money-td{min-width:540px}'
  style += '.note-form-iten{margin:0;overflow: hidden;}.note-form-iten-left{float: left;}.no-print{display: none;}'
  style += '.ivu-radio-checked {width: 10px;height: 10px;display: inline-block;border: 2px solid #000;border-radius: 50%;margin: 4px 4px 0;background: #000;vertical-align: top;}'
  style += '}'
  printJS({
    printable: id, // 要打印内容的id
    type: 'html',
    style,
    scanStyles: false
    // targetStyles: ['*']
  })
}

// 缴费单打印
export const printPayHtmlCustomStyle = (id) => {
  const style = '@page { margin: 0; } @media print { ' +
    '.print-pay-info-title {' +
      'font-size: 20px;' +
      'letter-spacing: 8px;' +
      'text-align: center;' +
      'padding-bottom: 10;' +
    '}' +
    '.print-pay-info {' +
      'width: 100% !important;' +
      'text-align: center;' +
      'font-family: verdana,arial,sans-serif;' +
      'font-size:11px;' +
      'color:#333;' +
      'border-width: 1px;' +
      'border-color: #333;' +
      'border-collapse: collapse;' +
      'table-layout: fixed;' +
    '}' +
    '.print-pay-info .text-align-left{' +
      'text-align: left;' +
    '}' +
    '.print-pay-info .min-height-18{' +
      'min-height: 16px;' +
      'max-height: 16px;' +
    '}' +
    '.print-pay-info tr:not(:first-child) th, .print-pay-info td {' +
      'border-width: 1px;' +
      'padding: 4px 8px;' +
      'border-style: solid;' +
      'border-color: #333;' +
    '}' +
    '.print-pay-info th {' +
      'background-color: #ffffff;' +
      'font-weight: normal;' +
    '}' +
    '.print-pay-info td {' +
      'background-color: #ffffff;' +
    '}' +
  '}'

  printJS({
    printable: id, // 要打印内容的id
    type: 'html',
    style: style,
    scanStyles: false
  })
}
