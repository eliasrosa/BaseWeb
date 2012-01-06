// var arr = [1,2,2,3,3,4,5,6,2,3,7,8,5,9];
// var unique = arr.unique();
// alert(unique);
//

Array.prototype.unique = function () {
    var r = new Array();
    o:for(var i = 0, n = this.length; i < n; i++)
    {
        for(var x = 0, y = r.length; x < y; x++)
        {
                if(r[x]==this[i])
                {
                //alert('this is a DUPE!');
                        continue o;
                }
        }
        r[r.length] = this[i];
    }
    return r;
}


