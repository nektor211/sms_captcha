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
p = zeros(5,7,maxk);
pt = zeros(5,7,maxk);
t = zeros(5,7,maxk);
e = zeros(5,7,maxk);
q = cell(5,7,maxk);

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
[trainInd,valInd,testInd] = dividerand(1079);
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
	net30scg.divideFcn = 'divideind';
	net30scg.divideParam.trainInd = trainInd;
	net30scg.divideParam.valInd = valInd;
	net30scg.divideParam.testInd = testInd;
	
	net30scg = init(net30scg);
	net30scg.trainParam.max_fail = 10;
	net30scg.trainParam.showWindow = false;
	[net30scg,tr] = train(net30scg,imgd,val);
	outputs = net30scg(imgd);
	performance30scg = perform(net30scg,val,net30scg(imgd));
	p(n,1,k) = performance30scg;
	pt(n,1,k) = perform(net30scg,val .*tr.testMask{1},outputs);
	t(n,1,k) = tr.time(tr.num_epochs+1);
	e(n,1,k) = tr.num_epochs;
	q{n,1,k} = net30scg;
	
	net30scg = init(net30scg);
	net30gdm = net30scg;
	net30gdm.trainFcn = 'traingd';
	net30gdm.trainParam.max_fail = 10;
	net30gdm.trainParam.showWindow = false;
	[net30gdm,tr] = train(net30gdm,imgd,val);
	outputs = net30gdm(imgd);
	performance30gdm = perform(net30gdm,val,net30gdm(imgd));
	p(n,2,k) = performance30gdm;
	pt(n,2,k) = perform(net30gdm,val .*tr.testMask{1},outputs);
	t(n,2,k) = tr.time(tr.num_epochs+1);
	e(n,2,k) = tr.num_epochs;
	q{n,2,k} = net30gdm;
	%plotconfusion(val,net30gdm(imgd))
	%pause
	if p(n,2,k) <0.0000
		plotconfusion(val,net30gdm(imgd))
		p(n,2,k)
		pause
	end
	
	net30gdm = net30scg;
	net30gdm.trainFcn = 'traingdm';
	net30gdm.trainParam.max_fail = 10;
	net30gdm.trainParam.showWindow = false;
	[net30gdm,tr] = train(net30gdm,imgd,val);
	outputs = net30gdm(imgd);
	performance30gdm = perform(net30gdm,val,net30gdm(imgd));
	p(n,3,k) = performance30gdm;
	pt(n,3,k) = perform(net30gdm,val .*tr.testMask{1},outputs);
	t(n,3,k) = tr.time(tr.num_epochs+1);
	e(n,3,k) = tr.num_epochs;
	q{n,3,k} = net30gdm;
	%plotconfusion(val,net30gdm(imgd))
	%pause
	if p(n,3,k) <0.0000
		plotconfusion(val,net30gdm(imgd))
		p(n,3,k)
		pause
	end
	
	net30gdm = net30scg;
	net30gdm.trainFcn = 'traingdm';
	net30gdm.trainParam.max_fail = 10;
	net30gdm.trainParam.mc = 0.7;
	net30gdm.trainParam.showWindow = false;
	[net30gdm,tr] = train(net30gdm,imgd,val);
	outputs = net30gdm(imgd);
	performance30gdm = perform(net30gdm,val,net30gdm(imgd));
	p(n,4,k) = performance30gdm;
	pt(n,4,k) = perform(net30gdm,val .*tr.testMask{1},outputs);
	t(n,4,k) = tr.time(tr.num_epochs+1);
	e(n,4,k) = tr.num_epochs;
	q{n,4,k} = net30gdm;
	%plotconfusion(val,net30gdm(imgd))
	%pause
	if p(n,4,k) <0.0000
		plotconfusion(val,net30gdm(imgd))
		p(n,4,k)
		pause
	end
	
	net30gdx = net30scg;
	net30gdx.trainFcn = 'traingdx';
	net30gdx.trainParam.max_fail = 10;
	net30gdx.trainParam.showWindow = false;
	[net30gdx,tr] = train(net30gdx,imgd,val);
	outputs = net30gdx(imgd);
	performance30gdx = perform(net30gdx,val,net30gdx(imgd));
	p(n,5,k) =  performance30gdx;
	pt(n,5,k) = perform(net30gdx,val .*tr.testMask{1},outputs);
	t(n,5,k) = tr.time(tr.num_epochs+1);
	e(n,5,k) = tr.num_epochs;
	q{n,5,k} = net30gdx;
	%plotconfusion(val,net30gdx(imgd))
	%pause
	if p(n,5,k) <0.0000
		plotconfusion(val,net30gdx(imgd))
		p(n,5,k)
		pause
	end
	
	net30gdx = net30scg;
	net30gdx.trainFcn = 'traingdx';
	net30gdx.trainParam.max_fail = 10;
	net30gdx.trainParam.mc = 0.7;
	net30gdx.trainParam.showWindow = false;
	[net30gdx,tr] = train(net30gdx,imgd,val);
	outputs = net30gdx(imgd);
	performance30gdx = perform(net30gdx,val,net30gdx(imgd));
	p(n,6,k) =  performance30gdx;
	pt(n,5,k) = perform(net30gdx,val .*tr.testMask{1},outputs);
	t(n,6,k) = tr.time(tr.num_epochs+1);
	e(n,6,k) = tr.num_epochs;
	q{n,6,k} = net30gdx;
	%plotconfusion(val,net30gdx(imgd))
	%pause
	if p(n,6,k) <0.0000
		plotconfusion(val,net30gdx(imgd))
		p(n,6,k)
		pause
	end
	
	net30gdx = net30scg;
	net30gdx.trainFcn = 'traingdx';
	net30gdx.trainParam.max_fail = 10;
	net30gdx.trainParam.mc = 0.99;
	net30gdx.trainParam.showWindow = false;
	[net30gdx,tr] = train(net30gdx,imgd,val);
	outputs = net30gdx(imgd);
	performance30gdx = perform(net30gdx,val,net30gdx(imgd));
	p(n,7,k) =  performance30gdx;
	pt(n,5,k) = perform(net30gdx,val .*tr.testMask{1},outputs);
	t(n,7,k) = tr.time(tr.num_epochs+1);
	e(n,7,k) = tr.num_epochs;
	q{n,7,k} = net30gdx;
	if p(n,7,k) <0.0000
		plotconfusion(val,net30gdx(imgd))
		p(n,7,k)
		pause
	end
end
end
ptmin = min(pt,[],3)
ptmean = mean(pt,3)
pmin = min(p,[],3)
pmean = mean(p,3)
emin = min(e,[],3)
emean = mean(e,3)
tmin = min(t,[],3)
tmean = mean(t,3)
save('p2','p');
save('e2','e');
save('t2','t');
save('q2','q');
save('pt2','pt');

%nprtool
