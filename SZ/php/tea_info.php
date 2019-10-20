<?php
/*
tea_info.php说明：
实现教师身份信息显示和总学生信息显示功能。
实现：从数据库获取相应教师id的附加信息，同时计算所有学生信息
输入：
id（教师工号;网页端标识：id)
输出：
name(教师名)
que_num（所有学生总做题数）
percent（所有学生总正确率）
timer（所有学生总做题时长）
*/
header('Content-type:text/json;charset=utf-8');
//网页输入读取
$id=$_POST['id'];
//连接数据库
$link_tea=mysqli_connect(
    'localhost',
    'root',
    '2925473239zjx',
    'teachers'
);
$link_stu=mysqli_connect(
    'localhost',
    'root',
    '2925473239zjx',
    'students'
);
//初始数据全部置零
$str_id="".$id;
$que_num=0;
$percent="";
$timer="";
$data=null;
$name="";
$second=0;
$minute=0;
$hour=0;

if($link_tea&&$link_stu){
    //获取老师姓名
    $reuslt=mysqli_fetch_array(mysqli_query($link_tea,"select * from teacher_info where id='$id'"));
    if($reuslt)
        $name=$reuslt["name"];
    //获取数据库中学生总做题数，平均正确率，做题总时间
    $que_num=mysqli_fetch_array(mysqli_query($link_stu,"SELECT sum(que_num) FROM stu_info"))[0];
    $right_num=mysqli_fetch_array(mysqli_query($link_stu,"SELECT sum(right_num) FROM stu_info"))[0];
    if($que_num!=0)
            $percent="".round((float)$right_num/$que_num*100,2)."%";
    else
            $percent=0;
    $result=mysqli_query($link_stu,"SELECT timer FROM stu_info");//获取表中所有学生的做题总时间
    if($result){
        $row=mysqli_fetch_all($result);
        $row_num=sizeof($row);
        for($i=0;$i<$row_num;$i++){//循环累加计算总做题时间
            $second+=intval(substr($row[$i][0],0,2));
            if($second>=60){
                $minute++;
                $second-=60;
            }
            $minute+=intval(substr($row[$i][0],2,2));
            if($minute>=60){
                $hour++;
                $minute-=60;
            }
            $hour+=intval(substr($row[$i][0],4));
        }
    }
    $timer="".$hour."时".$minute."分".$second."秒";
    $data='{name:"'.$name.'",que_num:"'.$que_num.'",percent:"'.$percent.'",timer:"'.$timer.'"}';
}
//返回json字符串
echo json_encode($data);
//关闭数据库
mysqli_close($link_stu);
mysqli_close($link_tea);
?>