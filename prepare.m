fid = fopen('data.txt', 'r');
data = textscan(fid, '%d%s', 'Delimiter','\t','HeaderLines',0)
%for i = 1:size(data,1)
%	print data(1,i)
%end
