
/* 
  获取url参数
*/
export const getQueryVariable = (variable) => {
  let query = window.location.search.substring(1);
  let vars = query.split("&");
  for (let i=0;i<vars.length;i++) {
    let pair = vars[i].split("=");
    if(pair[0] === variable){return pair[1];}
  }
  return '';
}

// 返回路由
export const recursiveRouter = (routerData) => {
  if (!routerData) return routerData
  for (var i = 0; i < routerData.length; i++) {
    let str = routerData[i]['component']
    routerData[i].component = () => import(`@/${str}`)
    if (routerData[i].children.length > 0) {
      recursiveRouter(routerData[i].children)
    }
  }
  return routerData
}



export const analyzeIDCard = (IDCard) => { // 身份证识别性别，年龄
  let sexAndAge = {}
  // 获取用户身份证号码
  let userCard = IDCard
  // 如果身份证号码为undefind则返回空
  if (!userCard) {
    return sexAndAge
  }
  // 获取性别
  if (parseInt(userCard.substr(16, 1)) % 2 === 1) {
    sexAndAge.sex = '0' // 男
    sexAndAge.sexTitle = '男'
  } else {
    sexAndAge.sex = '1' // 女
    sexAndAge.sexTitle = '女'
  }
  // 获取出生年月日
  // userCard.substring(6,10) + "-" + userCard.substring(10,12) + "-" + userCard.substring(12,14)
  let yearBirth = userCard.substring(6, 10)
  let monthBirth = userCard.substring(10, 12)
  let dayBirth = userCard.substring(12, 14)
  // 获取当前年月日并计算年龄
  let myDate = new Date()
  let monthNow = myDate.getMonth() + 1
  let dayNow = myDate.getDay()
  let age = myDate.getFullYear() - yearBirth
  if (monthNow < monthBirth || (monthNow === monthBirth && dayNow < dayBirth)) {
    age--
  }
  // 获取出生日期
  let birthdayno,birthdaytemp
  if(IDCard.length==18){
      birthdayno=IDCard.substring(6,14)
  }else if(IDCard.length==15){
      birthdaytemp=IDCard.substring(6,12)
      birthdayno="19"+birthdaytemp
  }else{
      alert("错误的身份证号码，请核对！")
      return false
  }
  let birthday = birthdayno.substring(0,4)+"-"+birthdayno.substring(4,6)+"-"+birthdayno.substring(6,8)
  // 得到年龄
  sexAndAge.birthday = birthday
  sexAndAge.age = age
  sexAndAge.studentIdCard = IDCard
  // 返回性别和年龄
  return sexAndAge
}