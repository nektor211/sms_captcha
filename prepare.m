clear;
pause on;
format compact;
fid = fopen('data.txt', 'r');
data = textscan(fid, '%d%s', 'Delimiter','\t','HeaderLines',0);
img = reshape(rgb2gray(imread(data{2}{1})),1500,1);
val = classify(data{1}');
for i = 2:size(data{1})
  img = [img reshape(rgb2gray(imread(data{2}{i})),1500,1)];
end

maxk = 1;
p = zeros(5,4,maxk);

c = size(data{1},1)
%c = 30
imgd = double(img(:,1:c));
val = val(:,1:c);
load net30
net30scg = net;
load net05
load net10
load net15
load net20
for k = 1:maxk
for n = 1:5
	[k n]
	if n == 1
		net30scg = net05;
	elseif n == 2
		net30scg = net10;
	elseif n == 3
		net30scg = net15;
	elseif n == 4
		net30scg = net20;
	else
		net30scg = net;
	end
	
	net30scg.trainParam.max_fail = 10;
	net30scg.trainParam.showWindow = false;
	[net30scg,tr] = train(net30scg,imgd,val);
	performance30scg = perform(net30scg,val,net30scg(imgd));
	p(n,1,k) = performance30scg;
	
	net30gdm = net30scg;
	net30gdm.trainFcn = 'traingdm';
	net30gdm.trainParam.max_fail = 10;
	net30gdm.trainParam.showWindow = false;
	[net30gdm,tr] = train(net30gdm,imgd,val);
	performance30gdm = perform(net30gdm,val,net30gdm(imgd));
	p(n,2,k) = performance30gdm;
	%plotconfusion(val,net30gdm(imgd))
	%pause
	
	net30gdm = net30scg;
	net30gdm.trainFcn = 'traingdm';
	net30gdm.trainParam.max_fail = 10;
	net30gdm.trainParam.lr = 0.1;
	net30gdm.trainParam.showWindow = false;
	[net30gdm,tr] = train(net30gdm,imgd,val);
	performance30gdm = perform(net30gdm,val,net30gdm(imgd));
	p(n,3,k) = performance30gdm;
	%plotconfusion(val,net30gdm(imgd))
	%pause
	
	net30gdx = net30scg;
	net30gdx.trainFcn = 'traingdx';
	net30gdx.trainParam.max_fail = 10;
	net30gdx.trainParam.showWindow = false;
	[net30gdx,tr] = train(net30gdx,imgd,val);
	performance30gdx = perform(net30gdx,val,net30gdx(imgd));
	p(n,4,k) =  performance30gdx;
	%plotconfusion(val,net30gdx(imgd))
	%pause
end
end
pmin = min(p,[],3)
pmean = mean(p,3)

%nprtool
