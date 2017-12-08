public static function levenshtein(s1:String, s2:String):int
    {      
        if (s1.length == 0 || s2.length == 0)
       return 0;
 
        var m:uint = s1.length + 1;
        var n:uint = s2.length + 1;
        var i:uint, j:uint, cost:uint;
        var d:Vector.<Vector.<int>> = new Vector.<Vector.<int>>();
 
        for (i = 0; i < m; i++)
        {
            d[i] = new Vector.<int>();
            for (j = 0; j < n; j++)
                d[i][j] = 0;
        }
 
        for (i = 0; i < m; d[i][0] = i++) ;
        for (j = 0; j < n; d[0][j] = j++) ;
 
        for (i = 1; i < m; i++)
        {
       for (j = 1; j < n; j++)
            {       
                cost = (s1.charAt(i - 1) == s2.charAt(j - 1)) ? 0 : 1;
                d[i][j] = Math.min(Math.min(d[i - 1][j] + 1,
                d[i][j - 1] + 1),
                d[i - 1][j - 1] + cost);
            }
        }
 
        return d[m-1][n-1];
    }