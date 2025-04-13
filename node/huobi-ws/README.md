价差
string
key         price_offset:symbol
value     价差

价差历史
zset 
key             price_offset_history:symbol
score         时间
member  价差

depth
price+=price_offset

trade
price+=price_offset

ticker
open+=price_offset_history:open
close+=price_offset_history:close
higt+=price_offset_history:higt
low+=price_offset_history:low

kline
