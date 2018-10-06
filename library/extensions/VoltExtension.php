<?php
/**
 * @desc volt模板引擎扩展
 * @author ZhaoYang
 * @date 2018年5月5日 下午9:06:34
 */
namespace Library\Extensions;

class VoltExtension {
    
    /**
     * @desc Triggered before trying to compile any function call in a template
     * @author ZhaoYang
     * @date 2018年5月5日 下午9:06:55
     */
    public function compileFunction($name, $arguments) {
        if (function_exists($name)) {
            return $name . '(' . $arguments . ')';
        }else if($name == 'bitwise'){
            return 'call_user_func(function ($lparam, $operator, $rparam = null) {
                switch ($operator) {
                    case "&":
                        return $lparam & $rparam;
                    case "|":
                        return $lparam | $rparam;
                    case "^":
                        return $lparam ^ $rparam;
                    case "~":
                        return ~$lparam;
                    case "<<":
                        return $lparam << $rparam;
                    case ">>":
                        return $lparam >> $rparam;
                    default:
                        throw new \Exception("bitwise函数未识别此运算符：$operator");
                }
            }, ' . $arguments . ')';
        }
    }
    
    /**
     * @desc Triggered before trying to compile any filter call in a template
     * @author ZhaoYang
     * @date 2018年5月5日 下午9:07:07
     */
    public function compileFilter($name, $arguments) {
        if (function_exists($name)) {
            return $name . '(' . $arguments . ')';
        }
    }
    
    /**
     * @desc Triggered before trying to compile any expression. This allows the developer to override operators
     * @author ZhaoYang
     * @date 2018年5月5日 下午9:07:21
     */
    public function resolveExpression($arguments) {
        
    }
    
    /**
     * @desc Triggered before trying to compile any expression. This allows the developer to override any statement
     * @author ZhaoYang
     * @date 2018年5月5日 下午9:07:43
     */
    public function compileStatement($arguments) {
        
    }
}